<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Content\StoreContentGenerationRequest;
use App\Models\ContentGeneration;
use App\Services\Content\ContentGenerationRunner;
use App\Support\AIPackageTextFormatter;
use App\Support\AIOutputStatuses;
use App\Support\AITextExportFormatter;
use App\Support\CurrentOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

class ContentGenerationController extends Controller
{
    public function __construct(
        private readonly ContentGenerationRunner $contentGenerationRunner,
        private readonly AITextExportFormatter $aiTextExportFormatter,
        private readonly AIPackageTextFormatter $aiPackageTextFormatter,
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
            'filters' => $request->only(['type', 'status', 'published', 'language', 'prompt_template_id', 'search']),
            'promptTemplates' => $organization->promptTemplates()
                ->orderBy('title')
                ->get(['id', 'title']),
            'availableTypes' => $this->availableTypes(),
            'availableStatuses' => AIOutputStatuses::labels(),
            'availablePublishedStates' => $this->availablePublishedStates(),
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
            'contentGeneration' => $this->findContentGeneration($contentGeneration),
        ]);
    }

    public function package(ContentGeneration $contentGeneration): View
    {
        return view('content.package', [
            'contentGeneration' => $this->findContentGeneration($contentGeneration),
        ]);
    }

    public function markReviewed(ContentGeneration $contentGeneration): RedirectResponse
    {
        $contentGeneration = $this->findContentGeneration($contentGeneration);
        $contentGeneration->update(['status' => AIOutputStatuses::REVIEWED]);

        return redirect()
            ->route('content.show', $contentGeneration)
            ->with('status', 'Content marked as reviewed.');
    }

    public function markApproved(ContentGeneration $contentGeneration): RedirectResponse
    {
        $contentGeneration = $this->findContentGeneration($contentGeneration);
        $contentGeneration->update(['status' => AIOutputStatuses::APPROVED]);

        return redirect()
            ->route('content.show', $contentGeneration)
            ->with('status', 'Content marked as approved.');
    }

    public function publish(ContentGeneration $contentGeneration): RedirectResponse
    {
        $contentGeneration = $this->findContentGeneration($contentGeneration);

        if (! AIOutputStatuses::canPublish($contentGeneration->status)) {
            return redirect()
                ->route('content.show', $contentGeneration)
                ->with('error', 'Content can only be published after review or approval.');
        }

        $contentGeneration->update([
            'is_published' => true,
            'published_at' => $contentGeneration->published_at ?? now(),
        ]);

        return redirect()
            ->route('content.show', $contentGeneration)
            ->with('status', 'Content published successfully.');
    }

    public function unpublish(ContentGeneration $contentGeneration): RedirectResponse
    {
        $contentGeneration = $this->findContentGeneration($contentGeneration);
        $contentGeneration->update([
            'is_published' => false,
        ]);

        return redirect()
            ->route('content.show', $contentGeneration)
            ->with('status', 'Content unpublished successfully.');
    }

    public function exportText(ContentGeneration $contentGeneration): Response
    {
        $contentGeneration = $this->findContentGeneration($contentGeneration);

        return response($this->aiTextExportFormatter->formatContent($contentGeneration), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function exportPackageText(ContentGeneration $contentGeneration): Response
    {
        $contentGeneration = $this->findContentGeneration($contentGeneration);

        return response($this->aiPackageTextFormatter->formatContent($contentGeneration), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    private function findContentGeneration(ContentGeneration $contentGeneration): ContentGeneration
    {
        return CurrentOrganization::get()->contentGenerations()
            ->with('promptTemplate')
            ->findOrFail($contentGeneration->getKey());
    }

    private function databaseStatusesFor(string $status): array
    {
        return AIOutputStatuses::databaseValuesFor($status);
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
            $builder->whereIn('status', $this->databaseStatusesFor($request->string('status')->toString()));
        });

        $query->when($request->filled('published'), function ($builder) use ($request): void {
            $builder->where('is_published', $request->string('published')->toString() === 'published');
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
     * @return array<string, string>
     */
    private function availablePublishedStates(): array
    {
        return [
            'published' => 'Published',
            'unpublished' => 'Unpublished',
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
