<?php

namespace App\Services\Offers;

use App\Models\OfferGeneration;
use App\Models\Organization;
use App\Models\PromptTemplate;
use App\Services\AI\AIExecutionService;
use App\Services\AI\AIRequestLoggerService;
use App\Services\AI\PromptTemplateRenderer;
use Throwable;

class OfferGenerationRunner
{
    public function __construct(
        private readonly AIExecutionService $aiExecutionService,
        private readonly AIRequestLoggerService $aiRequestLoggerService,
        private readonly PromptTemplateRenderer $promptTemplateRenderer,
    ) {}

    /**
     * @param  array{
     *     title: string|null,
     *     offer_type: string,
     *     context: string|null,
     *     input_payload: string|null
     * }  $attributes
     */
    public function run(Organization $organization, PromptTemplate $promptTemplate, array $attributes): OfferGeneration
    {
        $inputPayload = $this->parseInputPayload($attributes['input_payload'] ?? null);
        $context = $attributes['context'] ?? null;

        if (filled($context)) {
            $inputPayload['context'] = $context;
        }

        $renderedPrompt = $this->promptTemplateRenderer->render($promptTemplate, [
            'title' => $attributes['title'] ?? null,
            'type' => $attributes['offer_type'],
            'context' => $context,
            'input_payload' => $inputPayload,
        ]);

        try {
            $response = $this->aiExecutionService->execute('offers', $attributes['offer_type'], [
                'system_prompt' => $renderedPrompt['system_prompt'],
                'user_prompt' => $renderedPrompt['user_prompt'],
                'input_payload' => $inputPayload,
            ]);

            $offerGeneration = $organization->offerGenerations()->create([
                'prompt_template_id' => $promptTemplate->id,
                'title' => $attributes['title'] ?? null,
                'offer_type' => $attributes['offer_type'],
                'input_payload' => $inputPayload,
                'output_text' => $response['output_text'] ?? null,
                'model_name' => $response['model_name'] ?? null,
                'provider_name' => $response['provider_name'] ?? null,
                'status' => 'completed',
            ]);

            $this->aiRequestLoggerService->log($organization, [
                'module' => 'offers',
                'task_type' => $attributes['offer_type'],
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

            return $offerGeneration;
        } catch (Throwable $exception) {
            $this->aiRequestLoggerService->log($organization, [
                'module' => 'offers',
                'task_type' => $attributes['offer_type'],
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
            'offer_type' => $attributes['offer_type'],
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
