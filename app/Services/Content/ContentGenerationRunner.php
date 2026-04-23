<?php

namespace App\Services\Content;

use App\Models\ContentGeneration;
use App\Models\Organization;
use App\Models\PromptTemplate;
use App\Services\AI\AIExecutionService;
use App\Services\AI\AIRequestLoggerService;
use App\Services\AI\PromptTemplateRenderer;
use App\Support\AIOutputStatuses;
use Throwable;

class ContentGenerationRunner
{
    public function __construct(
        private readonly AIExecutionService $aiExecutionService,
        private readonly AIRequestLoggerService $aiRequestLoggerService,
        private readonly PromptTemplateRenderer $promptTemplateRenderer,
    ) {}

    /**
     * @param  array{
     *     title: string|null,
     *     type: string,
     *     language: string,
     *     tone: string|null,
     *     context: string|null,
     *     input_payload: string|null
     * }  $attributes
     */
    public function run(Organization $organization, PromptTemplate $promptTemplate, array $attributes): ContentGeneration
    {
        $inputPayload = $this->parseInputPayload($attributes['input_payload'] ?? null);
        $context = $attributes['context'] ?? null;

        if (filled($context)) {
            $inputPayload['context'] = $context;
        }

        $renderedPrompt = $this->promptTemplateRenderer->render($promptTemplate, [
            'title' => $attributes['title'] ?? null,
            'type' => $attributes['type'],
            'language' => $attributes['language'],
            'tone' => $attributes['tone'] ?? null,
            'context' => $context,
            'input_payload' => $inputPayload,
        ]);

        try {
            $response = $this->aiExecutionService->execute('content', $attributes['type'], [
                'system_prompt' => $renderedPrompt['system_prompt'],
                'user_prompt' => $renderedPrompt['user_prompt'],
                'input_payload' => $inputPayload,
            ]);

            $contentGeneration = $organization->contentGenerations()->create([
                'prompt_template_id' => $promptTemplate->id,
                'type' => $attributes['type'],
                'title' => $attributes['title'] ?? null,
                'input_payload' => $inputPayload,
                'output_text' => $response['output_text'] ?? null,
                'language' => $attributes['language'],
                'tone' => $attributes['tone'] ?? null,
                'model_name' => $response['model_name'] ?? null,
                'provider_name' => $response['provider_name'] ?? null,
                'status' => AIOutputStatuses::DRAFT,
            ]);

            $this->aiRequestLoggerService->log($organization, [
                'module' => 'content',
                'task_type' => $attributes['type'],
                'provider_name' => $response['provider_name'] ?? null,
                'model_name' => $response['model_name'] ?? null,
                'prompt_snapshot' => $response['prompt_snapshot'] ?? null,
                'input_payload' => $this->buildLogInputPayload($promptTemplate, $attributes, $inputPayload),
                'output_payload' => $response['output_text'] ?? null,
                'tokens_input' => $response['tokens_input'] ?? null,
                'tokens_output' => $response['tokens_output'] ?? null,
                'estimated_cost' => $response['estimated_cost'] ?? null,
                'latency_ms' => $response['latency_ms'] ?? null,
                'status' => 'completed',
                'error_message' => null,
            ]);

            return $contentGeneration;
        } catch (Throwable $exception) {
            $this->aiRequestLoggerService->log($organization, [
                'module' => 'content',
                'task_type' => $attributes['type'],
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
            'title' => $attributes['title'] ?? null,
            'type' => $attributes['type'],
            'language' => $attributes['language'],
            'tone' => $attributes['tone'] ?? null,
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
