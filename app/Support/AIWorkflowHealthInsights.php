<?php

namespace App\Support;

use App\Models\Organization;
use App\Models\PromptTemplate;
use Illuminate\Database\Eloquent\Builder;

class AIWorkflowHealthInsights
{
    public const STALE_DRAFT_DAYS = 7;

    public const SLOW_REQUEST_MS = 1000;

    /**
     * @return array{
     *     requests: array{failed:int,successful:int},
     *     review: array{draft:int,reviewed:int,approved:int},
     *     publishing: array{published:int,unpublished:int},
     *     staleDrafts: array{count:int,days:int,links:array<int, array{label:string,url:string,count:int}>},
     *     promptHealth: array{unused_active:int}
     * }
     */
    public function forDashboard(Organization $organization, Builder $accessiblePromptTemplates): array
    {
        return [
            'requests' => [
                'failed' => $organization->aiRequests()->where('status', 'failed')->count(),
                'successful' => $organization->aiRequests()->where('status', 'completed')->count(),
            ],
            'review' => [
                AIOutputStatuses::DRAFT => $this->draftOutputsCount($organization),
                AIOutputStatuses::REVIEWED => $this->reviewedOutputsCount($organization),
                AIOutputStatuses::APPROVED => $this->approvedOutputsCount($organization),
            ],
            'publishing' => [
                'published' => $this->publishedOutputsCount($organization),
                'unpublished' => $this->unpublishedOutputsCount($organization),
            ],
            'staleDrafts' => $this->staleDrafts($organization),
            'promptHealth' => [
                'unused_active' => $this->unusedActivePromptsCount($accessiblePromptTemplates),
            ],
        ];
    }

    public function staleDraftDays(): int
    {
        return self::STALE_DRAFT_DAYS;
    }

    public function slowRequestThresholdMs(): int
    {
        return self::SLOW_REQUEST_MS;
    }

    public function isSlowRequest(?int $latencyMs): bool
    {
        return $latencyMs !== null && $latencyMs >= self::SLOW_REQUEST_MS;
    }

    public function hasMissingOutputOrError(mixed $outputPayload, ?string $errorMessage): bool
    {
        if (filled($errorMessage)) {
            return true;
        }

        if (is_array($outputPayload)) {
            return $outputPayload === [];
        }

        return ! filled($outputPayload);
    }

    private function draftOutputsCount(Organization $organization): int
    {
        return $organization->contentGenerations()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::DRAFT))->count()
            + $organization->offerGenerations()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::DRAFT))->count()
            + $organization->strategyPlans()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::DRAFT))->count()
            + $organization->pageAnalyses()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::DRAFT))->count();
    }

    private function reviewedOutputsCount(Organization $organization): int
    {
        return $organization->contentGenerations()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::REVIEWED))->count()
            + $organization->offerGenerations()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::REVIEWED))->count()
            + $organization->strategyPlans()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::REVIEWED))->count()
            + $organization->pageAnalyses()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::REVIEWED))->count();
    }

    private function approvedOutputsCount(Organization $organization): int
    {
        return $organization->contentGenerations()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::APPROVED))->count()
            + $organization->offerGenerations()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::APPROVED))->count()
            + $organization->strategyPlans()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::APPROVED))->count()
            + $organization->pageAnalyses()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::APPROVED))->count();
    }

    private function publishedOutputsCount(Organization $organization): int
    {
        return $organization->contentGenerations()->where('is_published', true)->count()
            + $organization->offerGenerations()->where('is_published', true)->count()
            + $organization->strategyPlans()->where('is_published', true)->count()
            + $organization->pageAnalyses()->where('is_published', true)->count();
    }

    private function unpublishedOutputsCount(Organization $organization): int
    {
        return $organization->contentGenerations()->where('is_published', false)->count()
            + $organization->offerGenerations()->where('is_published', false)->count()
            + $organization->strategyPlans()->where('is_published', false)->count()
            + $organization->pageAnalyses()->where('is_published', false)->count();
    }

    /**
     * @return array{count:int,days:int,links:array<int, array{label:string,url:string,count:int}>}
     */
    private function staleDrafts(Organization $organization): array
    {
        $cutoff = now()->subDays(self::STALE_DRAFT_DAYS);
        $links = [
            [
                'label' => 'Content',
                'url' => route('content.index', ['status' => AIOutputStatuses::DRAFT]),
                'count' => $organization->contentGenerations()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::DRAFT))->where('created_at', '<=', $cutoff)->count(),
            ],
            [
                'label' => 'Offers',
                'url' => route('offers.index', ['status' => AIOutputStatuses::DRAFT]),
                'count' => $organization->offerGenerations()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::DRAFT))->where('created_at', '<=', $cutoff)->count(),
            ],
            [
                'label' => 'Plans',
                'url' => route('plans.index', ['status' => AIOutputStatuses::DRAFT]),
                'count' => $organization->strategyPlans()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::DRAFT))->where('created_at', '<=', $cutoff)->count(),
            ],
            [
                'label' => 'Analysis',
                'url' => route('analysis.index', ['status' => AIOutputStatuses::DRAFT]),
                'count' => $organization->pageAnalyses()->whereIn('status', AIOutputStatuses::databaseValuesFor(AIOutputStatuses::DRAFT))->where('created_at', '<=', $cutoff)->count(),
            ],
        ];

        return [
            'count' => collect($links)->sum('count'),
            'days' => self::STALE_DRAFT_DAYS,
            'links' => array_values(array_filter($links, static fn(array $link): bool => $link['count'] > 0)),
        ];
    }

    private function unusedActivePromptsCount(Builder $accessiblePromptTemplates): int
    {
        return (clone $accessiblePromptTemplates)
            ->where('is_active', true)
            ->whereDoesntHave('contentGenerations')
            ->whereDoesntHave('offerGenerations')
            ->whereDoesntHave('strategyPlans')
            ->whereDoesntHave('pageAnalyses')
            ->count();
    }
}
