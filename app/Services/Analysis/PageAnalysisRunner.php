<?php

namespace App\Services\Analysis;

use App\Models\Organization;
use App\Models\PageAnalysis;
use App\Models\PromptTemplate;
use App\Services\AI\AIExecutionService;
use App\Services\AI\AIRequestLoggerService;
use App\Services\AI\PromptTemplateRenderer;
use App\Support\AIOutputStatuses;
use Throwable;

class PageAnalysisRunner
{
    public function __construct(
        private readonly AIExecutionService $aiExecutionService,
        private readonly AIRequestLoggerService $aiRequestLoggerService,
        private readonly PromptTemplateRenderer $promptTemplateRenderer,
    ) {}

    /**
     * @param  array{
     *     page_title: string|null,
     *     page_url: string|null,
     *     page_type: string|null,
     *     page_content: string|null,
     *     context: string|null,
     *     input_payload: string|null
     * }  $attributes
     */
    public function run(Organization $organization, PromptTemplate $promptTemplate, array $attributes): PageAnalysis
    {
        $inputPayload = $this->parseInputPayload($attributes['input_payload'] ?? null);
        $pageContent = $attributes['page_content'] ?? null;
        $context = $attributes['context'] ?? null;

        if (filled($attributes['page_url'] ?? null)) {
            $inputPayload['page_url'] = $attributes['page_url'];
        }

        if (filled($pageContent)) {
            $inputPayload['page_content'] = $pageContent;
        }

        if (filled($context)) {
            $inputPayload['context'] = $context;
        }

        if (filled($attributes['page_type'] ?? null)) {
            $inputPayload['page_type'] = $attributes['page_type'];
        }

        $renderedPrompt = $this->promptTemplateRenderer->render($promptTemplate, [
            'title' => $attributes['page_title'] ?? null,
            'type' => $attributes['page_type'] ?? null,
            'context' => $context,
            'input_payload' => $inputPayload,
        ]);

        try {
            $response = $this->aiExecutionService->execute('analysis', $attributes['page_type'] ?? 'page_review', [
                'system_prompt' => $renderedPrompt['system_prompt'],
                'user_prompt' => $renderedPrompt['user_prompt'],
                'input_payload' => $inputPayload,
            ]);

            $recommendationsText = $response['output_text'] ?? null;

            $pageAnalysis = $organization->pageAnalyses()->create([
                'prompt_template_id' => $promptTemplate->id,
                'page_title' => $attributes['page_title'] ?? null,
                'page_url' => $attributes['page_url'] ?? null,
                'page_type' => $attributes['page_type'] ?? null,
                'input_payload' => $inputPayload,
                'findings_text' => null,
                'recommendations_text' => $recommendationsText,
                'score' => null,
                'model_name' => $response['model_name'] ?? null,
                'provider_name' => $response['provider_name'] ?? null,
                'status' => AIOutputStatuses::DRAFT,
            ]);

            $this->aiRequestLoggerService->log($organization, [
                'module' => 'analysis',
                'task_type' => $attributes['page_type'] ?? 'page_review',
                'provider_name' => $response['provider_name'] ?? null,
                'model_name' => $response['model_name'] ?? null,
                'prompt_snapshot' => $response['prompt_snapshot'] ?? null,
                'input_payload' => $this->buildLogInputPayload($promptTemplate, $attributes, $inputPayload),
                'output_payload' => $recommendationsText,
                'tokens_input' => $response['tokens_input'] ?? null,
                'tokens_output' => $response['tokens_output'] ?? null,
                'estimated_cost' => $response['estimated_cost'] ?? null,
                'latency_ms' => $response['latency_ms'] ?? null,
                'status' => 'completed',
                'error_message' => null,
            ]);

            return $pageAnalysis;
        } catch (Throwable $exception) {
            $this->aiRequestLoggerService->log($organization, [
                'module' => 'analysis',
                'task_type' => $attributes['page_type'] ?? 'page_review',
                'provider_name' => config('ai.default_provider'),
                'model_name' => config('ai.providers.openai.model'),
                'prompt_snapshot' => trim(
                    "system_prompt:\n" . ($renderedPrompt['system_prompt'] ?? '') . "\n\nuser_prompt:\n" . $renderedPrompt['user_prompt']
                ),
                'input_payload' => $this->buildLogInputPayload($promptTemplate, $attributes, $inputPayload),
                'output_payload' => null,
                'tokens_input' => null,
                'tokens_output' => null,
                'estimated_cost' => null,
                'latency_ms' => null,
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $inputPayload
     * @return array<string, mixed>
     */
    private function buildLogInputPayload(PromptTemplate $promptTemplate, array $attributes, array $inputPayload): array
    {
        return [
            'prompt_template_id' => $promptTemplate->id,
            'page_title' => $attributes['page_title'] ?? null,
            'page_url' => $attributes['page_url'] ?? null,
            'page_type' => $attributes['page_type'] ?? null,
            'payload' => $inputPayload,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseInputPayload(?string $payload): array
    {
        if (blank($payload)) {
            return [];
        }

        $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }
}
