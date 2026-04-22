<?php

namespace App\Http\Controllers\Analysis;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analysis\StorePageAnalysisRequest;
use App\Models\PageAnalysis;
use App\Services\Analysis\PageAnalysisRunner;
use App\Support\CurrentOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class PageAnalysisController extends Controller
{
    public function __construct(
        private readonly PageAnalysisRunner $pageAnalysisRunner,
    ) {}

    public function index(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $query = $organization->pageAnalyses()
            ->with('promptTemplate')
            ->latest();

        $this->applyIndexFilters($query, $request);

        return view('analysis.index', [
            'pageAnalyses' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['page_type', 'status', 'prompt_template_id', 'search']),
            'promptTemplates' => $organization->promptTemplates()
                ->whereIn('module', ['analysis', 'general'])
                ->orderBy('title')
                ->get(['id', 'title']),
            'availablePageTypes' => $this->availablePageTypes(),
            'availableStatuses' => [
                'completed' => 'Completed',
                'failed' => 'Failed',
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $sourcePageAnalysis = null;

        if ($request->filled('from')) {
            $sourcePageAnalysis = $organization->pageAnalyses()
                ->with('promptTemplate')
                ->findOrFail($request->integer('from'));
        }

        return view('analysis.create', [
            'promptTemplates' => $organization->promptTemplates()
                ->where('is_active', true)
                ->whereIn('module', ['analysis', 'general'])
                ->orderBy('title')
                ->get(),
            'pageTypes' => $this->availablePageTypes(),
            'defaults' => $this->buildCreateDefaults($sourcePageAnalysis),
            'sourcePageAnalysis' => $sourcePageAnalysis,
        ]);
    }

    public function store(StorePageAnalysisRequest $request): RedirectResponse
    {
        $organization = CurrentOrganization::get();
        $promptTemplate = $organization->promptTemplates()
            ->where('is_active', true)
            ->whereIn('module', ['analysis', 'general'])
            ->findOrFail($request->integer('prompt_template_id'));

        try {
            $pageAnalysis = $this->pageAnalysisRunner->run($organization, $promptTemplate, [
                'page_title' => $request->input('page_title'),
                'page_url' => $request->input('page_url'),
                'page_type' => $request->input('page_type'),
                'page_content' => $request->input('page_content'),
                'context' => $request->input('context'),
                'input_payload' => $request->input('input_payload'),
            ]);

            return redirect()->route('analysis.show', $pageAnalysis);
        } catch (Throwable $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'generation' => $exception->getMessage(),
                ]);
        }
    }

    public function show(PageAnalysis $pageAnalysis): View
    {
        return view('analysis.show', [
            'pageAnalysis' => CurrentOrganization::get()->pageAnalyses()
                ->with('promptTemplate')
                ->findOrFail($pageAnalysis->getKey()),
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<PageAnalysis>  $query
     */
    private function applyIndexFilters($query, Request $request): void
    {
        $query->when($request->filled('page_type'), function ($builder) use ($request): void {
            $builder->where('page_type', $request->string('page_type')->toString());
        });

        $query->when($request->filled('status'), function ($builder) use ($request): void {
            $builder->where('status', $request->string('status')->toString());
        });

        $query->when($request->filled('prompt_template_id'), function ($builder) use ($request): void {
            $builder->where('prompt_template_id', $request->integer('prompt_template_id'));
        });

        $query->when($request->filled('search'), function ($builder) use ($request): void {
            $search = $request->string('search')->trim()->toString();

            $builder->where('page_title', 'like', "%{$search}%");
        });
    }

    /**
     * @return array<string, string>
     */
    private function availablePageTypes(): array
    {
        return [
            'homepage' => 'Homepage',
            'landing_page' => 'Landing Page',
            'product_page' => 'Product Page',
            'template_page' => 'Template Page',
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private function buildCreateDefaults(?PageAnalysis $pageAnalysis): array
    {
        if ($pageAnalysis === null) {
            return [
                'prompt_template_id' => null,
                'page_title' => null,
                'page_url' => null,
                'page_type' => null,
                'page_content' => null,
                'context' => null,
                'input_payload' => '{}',
            ];
        }

        $inputPayload = $pageAnalysis->input_payload ?? [];
        $pageContent = is_array($inputPayload) ? ($inputPayload['page_content'] ?? null) : null;
        $context = is_array($inputPayload) ? ($inputPayload['context'] ?? null) : null;

        if (is_array($inputPayload)) {
            unset($inputPayload['page_content'], $inputPayload['context'], $inputPayload['page_url'], $inputPayload['page_type']);
        }

        return [
            'prompt_template_id' => (string) $pageAnalysis->prompt_template_id,
            'page_title' => $pageAnalysis->page_title,
            'page_url' => $pageAnalysis->page_url,
            'page_type' => $pageAnalysis->page_type,
            'page_content' => is_string($pageContent) ? $pageContent : null,
            'context' => is_string($context) ? $context : null,
            'input_payload' => $this->formatInputPayload(is_array($inputPayload) ? $inputPayload : null),
        ];
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
