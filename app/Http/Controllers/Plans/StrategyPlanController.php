<?php

namespace App\Http\Controllers\Plans;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plans\StoreStrategyPlanRequest;
use App\Models\StrategyPlan;
use App\Services\Plans\StrategyPlanRunner;
use App\Support\CurrentOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class StrategyPlanController extends Controller
{
    public function __construct(
        private readonly StrategyPlanRunner $strategyPlanRunner,
    ) {}

    public function index(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $query = $organization->strategyPlans()
            ->with('promptTemplate')
            ->latest();

        $this->applyIndexFilters($query, $request);

        return view('plans.index', [
            'strategyPlans' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['period_type', 'status', 'prompt_template_id', 'search']),
            'promptTemplates' => $organization->promptTemplates()
                ->whereIn('module', ['plans', 'general'])
                ->orderBy('title')
                ->get(['id', 'title']),
            'availablePeriodTypes' => $this->availablePeriodTypes(),
            'availableStatuses' => [
                'completed' => 'Completed',
                'failed' => 'Failed',
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $sourceStrategyPlan = null;

        if ($request->filled('from')) {
            $sourceStrategyPlan = $organization->strategyPlans()
                ->with('promptTemplate')
                ->findOrFail($request->integer('from'));
        }

        return view('plans.create', [
            'promptTemplates' => $organization->promptTemplates()
                ->where('is_active', true)
                ->whereIn('module', ['plans', 'general'])
                ->orderBy('title')
                ->get(),
            'periodTypes' => $this->availablePeriodTypes(),
            'defaults' => $this->buildCreateDefaults($sourceStrategyPlan),
            'sourceStrategyPlan' => $sourceStrategyPlan,
        ]);
    }

    public function store(StoreStrategyPlanRequest $request): RedirectResponse
    {
        $organization = CurrentOrganization::get();
        $promptTemplate = $organization->promptTemplates()
            ->where('is_active', true)
            ->whereIn('module', ['plans', 'general'])
            ->findOrFail($request->integer('prompt_template_id'));

        try {
            $strategyPlan = $this->strategyPlanRunner->run($organization, $promptTemplate, [
                'title' => $request->input('title'),
                'period_type' => $request->input('period_type'),
                'goals' => $this->parseGoals($request->input('goals')),
                'context' => $request->input('context'),
                'input_payload' => $request->input('input_payload'),
            ]);

            return redirect()->route('plans.show', $strategyPlan);
        } catch (Throwable $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'generation' => $exception->getMessage(),
                ]);
        }
    }

    public function show(StrategyPlan $strategyPlan): View
    {
        return view('plans.show', [
            'strategyPlan' => CurrentOrganization::get()->strategyPlans()
                ->with('promptTemplate')
                ->findOrFail($strategyPlan->getKey()),
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<StrategyPlan>  $query
     */
    private function applyIndexFilters($query, Request $request): void
    {
        $query->when($request->filled('period_type'), function ($builder) use ($request): void {
            $builder->where('period_type', $request->string('period_type')->toString());
        });

        $query->when($request->filled('status'), function ($builder) use ($request): void {
            $builder->where('status', $request->string('status')->toString());
        });

        $query->when($request->filled('prompt_template_id'), function ($builder) use ($request): void {
            $builder->where('prompt_template_id', $request->integer('prompt_template_id'));
        });

        $query->when($request->filled('search'), function ($builder) use ($request): void {
            $search = $request->string('search')->trim()->toString();

            $builder->where('title', 'like', "%{$search}%");
        });
    }

    /**
     * @return array<string, string>
     */
    private function availablePeriodTypes(): array
    {
        return [
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'campaign' => 'Campaign',
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private function buildCreateDefaults(?StrategyPlan $strategyPlan): array
    {
        if ($strategyPlan === null) {
            return [
                'prompt_template_id' => null,
                'title' => null,
                'period_type' => 'weekly',
                'goals' => null,
                'context' => null,
                'input_payload' => '{}',
            ];
        }

        $inputPayload = $strategyPlan->input_payload ?? [];

        if (is_array($inputPayload) && array_key_exists('context', $inputPayload)) {
            unset($inputPayload['context']);
        }

        return [
            'prompt_template_id' => (string) $strategyPlan->prompt_template_id,
            'title' => $strategyPlan->title,
            'period_type' => $strategyPlan->period_type,
            'goals' => $this->formatGoals($strategyPlan->goals_json),
            'context' => is_array($strategyPlan->input_payload) ? ($strategyPlan->input_payload['context'] ?? null) : null,
            'input_payload' => $this->formatInputPayload(is_array($inputPayload) ? $inputPayload : null),
        ];
    }

    /**
     * @return list<string>
     */
    private function parseGoals(?string $goals): array
    {
        if ($goals === null) {
            return [];
        }

        $parsedGoals = preg_split('/\r\n|\r|\n/', $goals) ?: [];

        return array_values(array_filter(array_map(static fn(string $goal): string => trim($goal), $parsedGoals), static fn(string $goal): bool => $goal !== ''));
    }

    /**
     * @param  array<int, string>|null  $goals
     */
    private function formatGoals(?array $goals): ?string
    {
        if ($goals === null || $goals === []) {
            return null;
        }

        return implode(PHP_EOL, $goals);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function formatInputPayload(?array $payload): string
    {
        if ($payload === null || $payload === []) {
            return '{}';
        }

        return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';
    }
}
