<?php

namespace App\Http\Controllers\Analysis;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analysis\StorePageAnalysisRequest;
use App\Models\PageAnalysis;
use App\Services\Analysis\PageAnalysisRunner;
use App\Support\AIPackageTextFormatter;
use App\Support\AIOutputStatuses;
use App\Support\AITextExportFormatter;
use App\Support\CurrentOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

class PageAnalysisController extends Controller
{
    public function __construct(
        private readonly PageAnalysisRunner $pageAnalysisRunner,
        private readonly AITextExportFormatter $aiTextExportFormatter,
        private readonly AIPackageTextFormatter $aiPackageTextFormatter,
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
            'filters' => $request->only(['page_type', 'status', 'published', 'prompt_template_id', 'search']),
            'promptTemplates' => $organization->promptTemplates()
                ->whereIn('module', ['analysis', 'general'])
                ->orderBy('title')
                ->get(['id', 'title']),
            'availablePageTypes' => $this->availablePageTypes(),
            'availableStatuses' => AIOutputStatuses::labels(),
            'availablePublishedStates' => $this->availablePublishedStates(),
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
            'pageAnalysis' => $this->findPageAnalysis($pageAnalysis),
        ]);
    }

    public function package(PageAnalysis $pageAnalysis): View
    {
        return view('analysis.package', [
            'pageAnalysis' => $this->findPageAnalysis($pageAnalysis),
        ]);
    }

    public function markReviewed(PageAnalysis $pageAnalysis): RedirectResponse
    {
        $pageAnalysis = $this->findPageAnalysis($pageAnalysis);
        $pageAnalysis->update(['status' => AIOutputStatuses::REVIEWED]);

        return redirect()
            ->route('analysis.show', $pageAnalysis)
            ->with('status', 'Analysis marked as reviewed.');
    }

    public function markApproved(PageAnalysis $pageAnalysis): RedirectResponse
    {
        $pageAnalysis = $this->findPageAnalysis($pageAnalysis);
        $pageAnalysis->update(['status' => AIOutputStatuses::APPROVED]);

        return redirect()
            ->route('analysis.show', $pageAnalysis)
            ->with('status', 'Analysis marked as approved.');
    }

    public function publish(PageAnalysis $pageAnalysis): RedirectResponse
    {
        $pageAnalysis = $this->findPageAnalysis($pageAnalysis);

        if (! AIOutputStatuses::canPublish($pageAnalysis->status)) {
            return redirect()
                ->route('analysis.show', $pageAnalysis)
                ->with('error', 'Analysis can only be published after review or approval.');
        }

        $pageAnalysis->update([
            'is_published' => true,
            'published_at' => $pageAnalysis->published_at ?? now(),
        ]);

        return redirect()
            ->route('analysis.show', $pageAnalysis)
            ->with('status', 'Analysis published successfully.');
    }

    public function unpublish(PageAnalysis $pageAnalysis): RedirectResponse
    {
        $pageAnalysis = $this->findPageAnalysis($pageAnalysis);
        $pageAnalysis->update([
            'is_published' => false,
        ]);

        return redirect()
            ->route('analysis.show', $pageAnalysis)
            ->with('status', 'Analysis unpublished successfully.');
    }

    public function exportText(PageAnalysis $pageAnalysis): Response
    {
        $pageAnalysis = $this->findPageAnalysis($pageAnalysis);

        return response($this->aiTextExportFormatter->formatPageAnalysis($pageAnalysis), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function exportPackageText(PageAnalysis $pageAnalysis): Response
    {
        $pageAnalysis = $this->findPageAnalysis($pageAnalysis);

        return response($this->aiPackageTextFormatter->formatPageAnalysis($pageAnalysis), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    private function findPageAnalysis(PageAnalysis $pageAnalysis): PageAnalysis
    {
        return CurrentOrganization::get()->pageAnalyses()
            ->with('promptTemplate')
            ->findOrFail($pageAnalysis->getKey());
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
            $builder->whereIn('status', AIOutputStatuses::databaseValuesFor($request->string('status')->toString()));
        });

        $query->when($request->filled('published'), function ($builder) use ($request): void {
            $builder->where('is_published', $request->string('published')->toString() === 'published');
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
