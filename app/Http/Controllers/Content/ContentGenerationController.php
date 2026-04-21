<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Content\StoreContentGenerationRequest;
use App\Models\ContentGeneration;
use App\Services\Content\ContentGenerationRunner;
use App\Support\CurrentOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ContentGenerationController extends Controller
{
    public function __construct(
        private readonly ContentGenerationRunner $contentGenerationRunner,
    ) {}

    public function index(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $query = $organization->contentGenerations()
            ->with('promptTemplate')
            ->latest();

        $this->applyIndexFilters($query, $request);

        return view('content.index', [
            'contentGenerations' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['type', 'status', 'language', 'prompt_template_id', 'search']),
            'promptTemplates' => $organization->promptTemplates()
                ->orderBy('title')
                ->get(['id', 'title']),
            'availableTypes' => $this->availableTypes(),
            'availableStatuses' => [
                'completed' => 'Completed',
                'failed' => 'Failed',
            ],
            'availableLanguages' => $organization->contentGenerations()
                ->whereNotNull('language')
                ->where('language', '!=', '')
                ->distinct()
                ->orderBy('language')
                ->pluck('language'),
        ]);
    }

    public function create(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $sourceContentGeneration = null;

        if ($request->filled('from')) {
            $sourceContentGeneration = $organization->contentGenerations()
                ->with('promptTemplate')
                ->findOrFail($request->integer('from'));
        }

        return view('content.create', [
            'promptTemplates' => $organization->promptTemplates()
                ->where('is_active', true)
                ->orderBy('title')
                ->get(),
            'contentTypes' => $this->availableTypes(),
            'defaults' => $this->buildCreateDefaults($sourceContentGeneration),
            'sourceContentGeneration' => $sourceContentGeneration,
        ]);
    }

    public function store(StoreContentGenerationRequest $request): RedirectResponse
    {
        $organization = CurrentOrganization::get();
        $promptTemplate = $organization->promptTemplates()
            ->where('is_active', true)
            ->findOrFail($request->integer('prompt_template_id'));

        try {
            $contentGeneration = $this->contentGenerationRunner->run($organization, $promptTemplate, [
                'title' => $request->input('title'),
                'type' => $request->input('type'),
                'language' => $request->input('language'),
                'tone' => $request->input('tone'),
                'context' => $request->input('context'),
                'input_payload' => $request->input('input_payload'),
            ]);

            return redirect()->route('content.show', $contentGeneration);
        } catch (Throwable $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'generation' => $exception->getMessage(),
                ]);
        }
    }

    public function show(ContentGeneration $contentGeneration): View
    {
        return view('content.show', [
            'contentGeneration' => CurrentOrganization::get()->contentGenerations()
                ->with('promptTemplate')
                ->findOrFail($contentGeneration->getKey()),
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<ContentGeneration>  $query
     */
    private function applyIndexFilters($query, Request $request): void
    {
        $query->when($request->filled('type'), function ($builder) use ($request): void {
            $builder->where('type', $request->string('type')->toString());
        });

        $query->when($request->filled('status'), function ($builder) use ($request): void {
            $builder->where('status', $request->string('status')->toString());
        });

        $query->when($request->filled('language'), function ($builder) use ($request): void {
            $builder->where('language', $request->string('language')->toString());
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
    private function availableTypes(): array
    {
        return [
            'social_post' => 'Social Post',
            'ad_copy' => 'Ad Copy',
            'landing_copy' => 'Landing Copy',
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private function buildCreateDefaults(?ContentGeneration $contentGeneration): array
    {
        if ($contentGeneration === null) {
            return [
                'prompt_template_id' => null,
                'title' => null,
                'type' => 'social_post',
                'language' => 'ar',
                'tone' => null,
                'context' => null,
                'input_payload' => '{}',
            ];
        }

        return [
            'prompt_template_id' => (string) $contentGeneration->prompt_template_id,
            'title' => $contentGeneration->title,
            'type' => $contentGeneration->type,
            'language' => $contentGeneration->language,
            'tone' => $contentGeneration->tone,
            'context' => null,
            'input_payload' => $this->formatInputPayload($contentGeneration->input_payload),
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
