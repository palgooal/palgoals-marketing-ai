<?php

namespace App\Http\Controllers\Prompts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prompts\StorePromptTemplateRequest;
use App\Http\Requests\Prompts\UpdatePromptTemplateRequest;
use App\Models\PromptTemplate;
use App\Models\PromptTemplateVersion;
use App\Support\PromptComparison;
use App\Support\CurrentOrganization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromptTemplateController extends Controller
{
    public function __construct(
        private readonly PromptComparison $promptComparison,
    ) {}

    public function index(Request $request): View
    {
        $organization = CurrentOrganization::get();
        $query = $this->accessiblePromptTemplates($organization->getKey())
            ->withCount(['contentGenerations', 'offerGenerations', 'strategyPlans', 'pageAnalyses'])
            ->orderBy('module')
            ->orderByDesc('organization_id')
            ->orderBy('title');

        $this->applyIndexFilters($query, $request);

        return view('prompts.index', [
            'promptTemplates' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['module', 'active', 'usage', 'search']),
            'modules' => $this->accessiblePromptTemplates($organization->getKey())
                ->select('module')
                ->distinct()
                ->orderBy('module')
                ->pluck('module'),
        ]);
    }

    public function create(): View
    {
        return view('prompts.create');
    }

    public function store(StorePromptTemplateRequest $request): RedirectResponse
    {
        CurrentOrganization::get()->promptTemplates()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('prompts.index')
            ->with('status', 'Prompt template created successfully.');
    }

    public function edit(PromptTemplate $promptTemplate): View
    {
        $promptTemplate = $this->findAccessiblePromptTemplate($promptTemplate)
            ->loadCount(['contentGenerations', 'offerGenerations', 'strategyPlans', 'pageAnalyses'])
            ->load([
                'versions' => fn($query) => $query->latest('version_number')->limit(5),
            ]);

        $recentUsage = $this->recentUsageItems($promptTemplate);

        return view('prompts.edit', [
            'promptTemplate' => $promptTemplate,
            'usageSummary' => [
                'total' => $promptTemplate->content_generations_count
                    + $promptTemplate->offer_generations_count
                    + $promptTemplate->strategy_plans_count
                    + $promptTemplate->page_analyses_count,
                'content' => $promptTemplate->content_generations_count,
                'offers' => $promptTemplate->offer_generations_count,
                'plans' => $promptTemplate->strategy_plans_count,
                'analysis' => $promptTemplate->page_analyses_count,
                'last_used_at' => collect($recentUsage)
                    ->pluck('created_at')
                    ->filter()
                    ->sortDesc()
                    ->first(),
            ],
            'recentUsage' => $recentUsage,
        ]);
    }

    public function showVersion(PromptTemplate $promptTemplate, PromptTemplateVersion $promptTemplateVersion): View
    {
        $promptTemplate = $this->findAccessiblePromptTemplate($promptTemplate);

        return view('prompts.version-preview', [
            'promptTemplate' => $promptTemplate,
            'promptTemplateVersion' => $this->findPromptTemplateVersion($promptTemplate, $promptTemplateVersion),
        ]);
    }

    public function compare(Request $request, PromptTemplate $promptTemplate): View
    {
        $promptTemplate = $this->findAccessiblePromptTemplate($promptTemplate)
            ->load([
                'versions' => fn ($query) => $query->latest('version_number')->limit(5),
            ]);

        $fromVersion = $this->resolveComparisonVersion(
            $promptTemplate,
            $request->filled('from_version_id') ? $request->integer('from_version_id') : null,
        );
        $toVersion = $this->resolveComparisonVersion(
            $promptTemplate,
            $request->filled('to_version_id') ? $request->integer('to_version_id') : null,
        );

        if ($fromVersion === null && $toVersion === null) {
            abort(404);
        }

        $comparison = $this->promptComparison->build($promptTemplate, $fromVersion, $toVersion);

        return view('prompts.compare', [
            'promptTemplate' => $promptTemplate,
            'comparison' => $comparison,
        ]);
    }

    public function update(UpdatePromptTemplateRequest $request, PromptTemplate $promptTemplate): RedirectResponse
    {
        $promptTemplate = $this->findAccessiblePromptTemplate($promptTemplate);

        $this->createVersionSnapshot($promptTemplate);

        $promptTemplate->update([
            ...$request->validated(),
            'version' => $promptTemplate->version + 1,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('prompts.edit', $promptTemplate)
            ->with('status', 'Prompt template updated and version snapshot saved successfully.');
    }

    public function revertVersion(PromptTemplate $promptTemplate, PromptTemplateVersion $promptTemplateVersion): RedirectResponse
    {
        $promptTemplate = $this->findAccessiblePromptTemplate($promptTemplate);
        $promptTemplateVersion = $this->findPromptTemplateVersion($promptTemplate, $promptTemplateVersion);

        $this->createVersionSnapshot($promptTemplate);

        $promptTemplate->update([
            'title' => $promptTemplateVersion->title,
            'description' => $promptTemplateVersion->description,
            'system_prompt' => $promptTemplateVersion->system_prompt,
            'user_prompt_template' => $promptTemplateVersion->user_prompt_template,
            'module' => $promptTemplateVersion->module,
            'version' => $promptTemplate->version + 1,
            'is_active' => $promptTemplate->is_active,
        ]);

        return redirect()
            ->route('prompts.edit', $promptTemplate)
            ->with('status', "Prompt template reverted from version {$promptTemplateVersion->version_number} and a new snapshot was saved successfully.");
    }

    public function duplicate(PromptTemplate $promptTemplate): RedirectResponse
    {
        $sourcePromptTemplate = $this->findAccessiblePromptTemplate($promptTemplate);

        $duplicate = PromptTemplate::query()->create([
            'organization_id' => $sourcePromptTemplate->organization_id,
            'key' => $this->makeDuplicateKey($sourcePromptTemplate->key),
            'title' => $this->makeDuplicateTitle($sourcePromptTemplate->title),
            'description' => $sourcePromptTemplate->description,
            'system_prompt' => $sourcePromptTemplate->system_prompt,
            'user_prompt_template' => $sourcePromptTemplate->user_prompt_template,
            'module' => $sourcePromptTemplate->module,
            'version' => 1,
            'is_active' => false,
        ]);

        return redirect()
            ->route('prompts.edit', $duplicate)
            ->with('status', 'Prompt template duplicated as a new inactive draft.');
    }

    public function toggleActive(PromptTemplate $promptTemplate): RedirectResponse
    {
        $promptTemplate = $this->findAccessiblePromptTemplate($promptTemplate);

        $promptTemplate->update([
            'is_active' => ! $promptTemplate->is_active,
        ]);

        return redirect()
            ->route('prompts.index')
            ->with('status', $promptTemplate->is_active
                ? 'Prompt template activated successfully.'
                : 'Prompt template deactivated successfully.');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<PromptTemplate>  $query
     */
    private function applyIndexFilters($query, Request $request): void
    {
        $query->when($request->filled('module'), function ($builder) use ($request): void {
            $builder->where('module', $request->string('module')->toString());
        });

        $query->when($request->filled('active'), function ($builder) use ($request): void {
            $builder->where('is_active', $request->string('active')->toString() === '1');
        });

        $query->when($request->filled('usage'), function ($builder) use ($request): void {
            $usage = $request->string('usage')->toString();

            if ($usage === 'used') {
                $builder->where(function ($usageQuery): void {
                    $usageQuery->whereHas('contentGenerations')
                        ->orWhereHas('offerGenerations')
                        ->orWhereHas('strategyPlans')
                        ->orWhereHas('pageAnalyses');
                });
            }

            if ($usage === 'unused') {
                $builder->whereDoesntHave('contentGenerations')
                    ->whereDoesntHave('offerGenerations')
                    ->whereDoesntHave('strategyPlans')
                    ->whereDoesntHave('pageAnalyses');
            }
        });

        $query->when($request->filled('search'), function ($builder) use ($request): void {
            $search = $request->string('search')->trim()->toString();

            $builder->where(function ($nestedQuery) use ($search): void {
                $nestedQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('key', 'like', "%{$search}%");
            });
        });
    }

    private function accessiblePromptTemplates(int $organizationId): Builder
    {
        return PromptTemplate::query()->where(function (Builder $query) use ($organizationId): void {
            $query
                ->where('organization_id', $organizationId)
                ->orWhereNull('organization_id');
        });
    }

    private function findAccessiblePromptTemplate(PromptTemplate $promptTemplate): PromptTemplate
    {
        return $this->accessiblePromptTemplates(CurrentOrganization::get()->getKey())
            ->findOrFail($promptTemplate->getKey());
    }

    private function findPromptTemplateVersion(PromptTemplate $promptTemplate, PromptTemplateVersion $promptTemplateVersion): PromptTemplateVersion
    {
        return $promptTemplate->versions()
            ->findOrFail($promptTemplateVersion->getKey());
    }

    private function resolveComparisonVersion(PromptTemplate $promptTemplate, ?int $versionId): ?PromptTemplateVersion
    {
        if ($versionId === null) {
            return null;
        }

        return $promptTemplate->versions()->findOrFail($versionId);
    }

    private function createVersionSnapshot(PromptTemplate $promptTemplate): PromptTemplateVersion
    {
        return $promptTemplate->versions()->create([
            'version_number' => $promptTemplate->version,
            'title' => $promptTemplate->title,
            'description' => $promptTemplate->description,
            'system_prompt' => $promptTemplate->system_prompt,
            'user_prompt_template' => $promptTemplate->user_prompt_template,
            'module' => $promptTemplate->module,
            'is_active' => $promptTemplate->is_active,
        ]);
    }

    /**
     * @return list<array{module:string,label:string,created_at:mixed,url:string}>
     */
    private function recentUsageItems(PromptTemplate $promptTemplate): array
    {
        $items = [];

        $latestContent = $promptTemplate->contentGenerations()->latest()->first(['id', 'title', 'created_at']);

        if ($latestContent !== null) {
            $items[] = [
                'module' => 'Content',
                'label' => $latestContent->title ?: 'Untitled content',
                'created_at' => $latestContent->created_at,
                'url' => route('content.show', $latestContent),
            ];
        }

        $latestOffer = $promptTemplate->offerGenerations()->latest()->first(['id', 'title', 'created_at']);

        if ($latestOffer !== null) {
            $items[] = [
                'module' => 'Offers',
                'label' => $latestOffer->title ?: 'Untitled offer',
                'created_at' => $latestOffer->created_at,
                'url' => route('offers.show', $latestOffer),
            ];
        }

        $latestPlan = $promptTemplate->strategyPlans()->latest()->first(['id', 'title', 'created_at']);

        if ($latestPlan !== null) {
            $items[] = [
                'module' => 'Plans',
                'label' => $latestPlan->title ?: 'Untitled plan',
                'created_at' => $latestPlan->created_at,
                'url' => route('plans.show', $latestPlan),
            ];
        }

        $latestAnalysis = $promptTemplate->pageAnalyses()->latest()->first(['id', 'page_title', 'created_at']);

        if ($latestAnalysis !== null) {
            $items[] = [
                'module' => 'Analysis',
                'label' => $latestAnalysis->page_title ?: 'Untitled analysis',
                'created_at' => $latestAnalysis->created_at,
                'url' => route('analysis.show', $latestAnalysis),
            ];
        }

        usort($items, static function (array $left, array $right): int {
            return ($right['created_at']?->getTimestamp() ?? 0) <=> ($left['created_at']?->getTimestamp() ?? 0);
        });

        return $items;
    }

    private function makeDuplicateKey(string $key): string
    {
        $baseKey = str($key)->finish('.copy')->toString();
        $candidateKey = $baseKey;
        $suffix = 2;

        while (PromptTemplate::query()->where('key', $candidateKey)->exists()) {
            $candidateKey = $baseKey . '.' . $suffix;
            $suffix++;
        }

        return $candidateKey;
    }

    private function makeDuplicateTitle(string $title): string
    {
        $baseTitle = str($title)->finish(' Copy')->toString();
        $candidateTitle = $baseTitle;
        $suffix = 2;

        while (PromptTemplate::query()->where('title', $candidateTitle)->exists()) {
            $candidateTitle = $baseTitle . ' ' . $suffix;
            $suffix++;
        }

        return $candidateTitle;
    }
}
