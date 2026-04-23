<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PromptTemplate;
use App\Support\AIWorkflowHealthInsights;
use App\Support\CurrentOrganization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AIWorkflowHealthInsights $aiWorkflowHealthInsights,
    ) {}

    public function __invoke(): View
    {
        $organization = CurrentOrganization::get()->load('brandProfile');
        $promptTemplates = $this->accessiblePromptTemplates($organization->getKey());
        $healthInsights = $this->aiWorkflowHealthInsights->forDashboard($organization, $promptTemplates);

        return view('dashboard.index', [
            'organization' => $organization,
            'latestContentGeneration' => $organization->contentGenerations()->latest()->first(),
            'latestOfferGeneration' => $organization->offerGenerations()->latest()->first(),
            'latestStrategyPlan' => $organization->strategyPlans()->latest()->first(),
            'latestPageAnalysis' => $organization->pageAnalyses()->latest()->first(),
            'promptInsights' => [
                'total' => (clone $promptTemplates)->count(),
                'active' => (clone $promptTemplates)->where('is_active', true)->count(),
                'unused' => (clone $promptTemplates)
                    ->whereDoesntHave('contentGenerations')
                    ->whereDoesntHave('offerGenerations')
                    ->whereDoesntHave('strategyPlans')
                    ->whereDoesntHave('pageAnalyses')
                    ->count(),
                'unused_active' => $healthInsights['promptHealth']['unused_active'],
            ],
            'workflowHealth' => $healthInsights['requests'],
            'publishingSummary' => $healthInsights['publishing'],
            'reviewSummary' => $healthInsights['review'],
            'staleDraftInsights' => $healthInsights['staleDrafts'],
        ]);
    }

    private function accessiblePromptTemplates(int $organizationId): Builder
    {
        return PromptTemplate::query()->where(function (Builder $query) use ($organizationId): void {
            $query
                ->where('organization_id', $organizationId)
                ->orWhereNull('organization_id');
        });
    }
}
