<?php

namespace App\Support;

use App\Models\ContentGeneration;
use App\Models\OfferGeneration;
use App\Models\PageAnalysis;
use App\Models\StrategyPlan;

class AIPackageTextFormatter
{
    public function formatContent(ContentGeneration $contentGeneration): string
    {
        return $this->buildPackageText(
            'Content Package',
            [
                'Title' => $contentGeneration->title,
                'Type' => $contentGeneration->type,
                'Status' => AIOutputStatuses::normalize($contentGeneration->status),
                'Published' => $this->publishedLabel($contentGeneration->is_published),
                'Published At' => $contentGeneration->published_at?->format('Y-m-d H:i'),
                'Language' => $contentGeneration->language,
                'Tone' => $contentGeneration->tone,
                'Provider' => $contentGeneration->provider_name,
                'Model' => $contentGeneration->model_name,
                'Prompt Template' => $contentGeneration->promptTemplate?->title,
                'Prompt Key' => $contentGeneration->promptTemplate?->key,
            ],
            $this->contextText($contentGeneration->input_payload),
            $contentGeneration->output_text,
            $contentGeneration->input_payload,
        );
    }

    public function formatOffer(OfferGeneration $offerGeneration): string
    {
        return $this->buildPackageText(
            'Offer Package',
            [
                'Title' => $offerGeneration->title,
                'Offer Type' => $offerGeneration->offer_type,
                'Status' => AIOutputStatuses::normalize($offerGeneration->status),
                'Published' => $this->publishedLabel($offerGeneration->is_published),
                'Published At' => $offerGeneration->published_at?->format('Y-m-d H:i'),
                'Provider' => $offerGeneration->provider_name,
                'Model' => $offerGeneration->model_name,
                'Prompt Template' => $offerGeneration->promptTemplate?->title,
                'Prompt Key' => $offerGeneration->promptTemplate?->key,
            ],
            $this->contextText($offerGeneration->input_payload),
            $offerGeneration->output_text,
            $offerGeneration->input_payload,
        );
    }

    public function formatStrategyPlan(StrategyPlan $strategyPlan): string
    {
        $context = $this->contextText($strategyPlan->input_payload);

        if (($strategyPlan->goals_json ?? []) !== []) {
            $context = trim($context . PHP_EOL . PHP_EOL . 'Goals' . PHP_EOL . '-----' . PHP_EOL . implode(PHP_EOL, $strategyPlan->goals_json));
        }

        return $this->buildPackageText(
            'Plan Package',
            [
                'Title' => $strategyPlan->title,
                'Period Type' => $strategyPlan->period_type,
                'Status' => AIOutputStatuses::normalize($strategyPlan->status),
                'Published' => $this->publishedLabel($strategyPlan->is_published),
                'Published At' => $strategyPlan->published_at?->format('Y-m-d H:i'),
                'Provider' => $strategyPlan->provider_name,
                'Model' => $strategyPlan->model_name,
                'Prompt Template' => $strategyPlan->promptTemplate?->title,
                'Prompt Key' => $strategyPlan->promptTemplate?->key,
            ],
            $context,
            $strategyPlan->output_text,
            $strategyPlan->input_payload,
        );
    }

    public function formatPageAnalysis(PageAnalysis $pageAnalysis): string
    {
        $output = trim(implode(PHP_EOL . PHP_EOL, array_filter([
            $pageAnalysis->findings_text ? "Findings\n--------\n{$pageAnalysis->findings_text}" : null,
            $pageAnalysis->recommendations_text ? "Recommendations\n---------------\n{$pageAnalysis->recommendations_text}" : null,
        ])));

        return $this->buildPackageText(
            'Analysis Package',
            [
                'Title' => $pageAnalysis->page_title,
                'Page Type' => $pageAnalysis->page_type,
                'Status' => AIOutputStatuses::normalize($pageAnalysis->status),
                'Published' => $this->publishedLabel($pageAnalysis->is_published),
                'Published At' => $pageAnalysis->published_at?->format('Y-m-d H:i'),
                'Page URL' => $pageAnalysis->page_url,
                'Score' => $pageAnalysis->score !== null ? (string) $pageAnalysis->score : null,
                'Provider' => $pageAnalysis->provider_name,
                'Model' => $pageAnalysis->model_name,
                'Prompt Template' => $pageAnalysis->promptTemplate?->title,
                'Prompt Key' => $pageAnalysis->promptTemplate?->key,
            ],
            $this->contextText($pageAnalysis->input_payload, ['context', 'page_content']),
            $output,
            $pageAnalysis->input_payload,
        );
    }

    /**
     * @param  array<string, string|null>  $meta
     * @param  array<string, mixed>|null  $payload
     */
    private function buildPackageText(string $packageTitle, array $meta, ?string $contextDetails, ?string $output, ?array $payload): string
    {
        $lines = [$packageTitle, str_repeat('=', strlen($packageTitle)), ''];

        foreach ($meta as $label => $value) {
            if (filled($value)) {
                $lines[] = $label . ': ' . $value;
            }
        }

        $this->appendSection($lines, 'Context Details', $contextDetails ?: 'No stored context details.');
        $this->appendSection($lines, 'Main Output', $output ?: 'No output available.');
        $this->appendSection($lines, 'Input Payload', $this->formatPayload($payload));

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param  list<string>  $lines
     */
    private function appendSection(array &$lines, string $title, string $body): void
    {
        $lines[] = '';
        $lines[] = $title;
        $lines[] = str_repeat('-', strlen($title));
        $lines[] = $body;
    }

    /**
     * @param  array<string, mixed>|null  $payload
     * @param  list<string>  $keys
     */
    private function contextText(?array $payload, array $keys = ['context']): ?string
    {
        if ($payload === null || $payload === []) {
            return null;
        }

        $lines = [];

        foreach ($keys as $key) {
            $value = $payload[$key] ?? null;

            if (is_array($value)) {
                $encoded = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                if ($encoded !== false) {
                    $lines[] = str($key)->replace('_', ' ')->title() . ':';
                    $lines[] = $encoded;
                }

                continue;
            }

            if (filled($value)) {
                $lines[] = str($key)->replace('_', ' ')->title() . ': ' . $value;
            }
        }

        return $lines === [] ? null : implode(PHP_EOL, $lines);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function formatPayload(?array $payload): string
    {
        if ($payload === null || $payload === []) {
            return 'No input payload stored.';
        }

        return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: 'No input payload stored.';
    }

    private function publishedLabel(bool $isPublished): string
    {
        return $isPublished ? 'Published' : 'Unpublished';
    }
}
