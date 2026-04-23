<?php

namespace App\Support;

use App\Models\ContentGeneration;
use App\Models\OfferGeneration;
use App\Models\PageAnalysis;
use App\Models\StrategyPlan;

class AITextExportFormatter
{
    public function formatContent(ContentGeneration $contentGeneration): string
    {
        return $this->buildText([
            'Title' => $contentGeneration->title,
            'Type' => $contentGeneration->type,
            'Status' => AIOutputStatuses::normalize($contentGeneration->status),
            'Provider' => $contentGeneration->provider_name,
            'Model' => $contentGeneration->model_name,
        ], $contentGeneration->output_text, 'Generated Output');
    }

    public function formatOffer(OfferGeneration $offerGeneration): string
    {
        return $this->buildText([
            'Title' => $offerGeneration->title,
            'Offer Type' => $offerGeneration->offer_type,
            'Status' => AIOutputStatuses::normalize($offerGeneration->status),
            'Provider' => $offerGeneration->provider_name,
            'Model' => $offerGeneration->model_name,
        ], $offerGeneration->output_text, 'Generated Output');
    }

    public function formatStrategyPlan(StrategyPlan $strategyPlan): string
    {
        return $this->buildText([
            'Title' => $strategyPlan->title,
            'Period Type' => $strategyPlan->period_type,
            'Status' => AIOutputStatuses::normalize($strategyPlan->status),
            'Provider' => $strategyPlan->provider_name,
            'Model' => $strategyPlan->model_name,
        ], $strategyPlan->output_text, 'Generated Output');
    }

    public function formatPageAnalysis(PageAnalysis $pageAnalysis): string
    {
        $body = trim(implode(PHP_EOL . PHP_EOL, array_filter([
            $pageAnalysis->findings_text ? "Findings\n--------\n{$pageAnalysis->findings_text}" : null,
            $pageAnalysis->recommendations_text ? "Recommendations\n---------------\n{$pageAnalysis->recommendations_text}" : null,
        ])));

        return $this->buildText([
            'Page Title' => $pageAnalysis->page_title,
            'Page URL' => $pageAnalysis->page_url,
            'Page Type' => $pageAnalysis->page_type,
            'Status' => AIOutputStatuses::normalize($pageAnalysis->status),
            'Provider' => $pageAnalysis->provider_name,
            'Model' => $pageAnalysis->model_name,
        ], $body, 'Generated Output');
    }

    /**
     * @param  array<string, string|null>  $meta
     */
    private function buildText(array $meta, ?string $output, string $outputLabel): string
    {
        $lines = [];

        foreach ($meta as $label => $value) {
            if (filled($value)) {
                $lines[] = $label . ': ' . $value;
            }
        }

        $lines[] = '';
        $lines[] = $outputLabel;
        $lines[] = str_repeat('-', strlen($outputLabel));
        $lines[] = $output ?: 'No output available.';

        return implode(PHP_EOL, $lines);
    }
}
