<?php

namespace App\Services\Plans;

use App\Models\Organization;
use App\Models\PromptTemplate;
use App\Models\StrategyPlan;
use App\Services\AI\AIExecutionService;
use App\Services\AI\AIRequestLoggerService;
use App\Services\AI\PromptTemplateRenderer;
use Throwable;

class StrategyPlanRunner
{
    public function __construct(
        private readonly AIExecutionService $aiExecutionService,
        private readonly AIRequestLoggerService $aiRequestLoggerService,
        private readonly PromptTemplateRenderer $promptTemplateRenderer,
    ) {}

    /**
     * @param  array{
     *     title: string|null,
     *     period_type: string,
     *     goals: list<string>,
     *     context: string|null,
     *     input_payload: string|null
     * }  $attributes
     */
    public function run(Organization $organization, PromptTemplate $promptTemplate, array $attributes): StrategyPlan
    {
        $goals = $attributes['goals'];
        $inputPayload = $this->parseInputPayload($attributes['input_payload'] ?? null);
        $context = $attributes['context'] ?? null;

        if (filled($context)) {
            $inputPayload['context'] = $context;
        }

        $renderPayload = $inputPayload;

        if ($goals !== []) {
            $renderPayload['goals'] = $goals;
        }

        $renderPayload['period_type'] = $attributes['period_type'];

        $renderedPrompt = $this->promptTemplateRenderer->render($promptTemplate, [
            'title' => $attributes['title'] ?? null,
            'type' => $attributes['period_type'],
            'context' => $context,
            'input_payload' => $renderPayload,
        ]);

        try {
            $response = $this->aiExecutionService->execute('plans', $attributes['period_type'], [
                'system_prompt' => $renderedPrompt['system_prompt'],
                'user_prompt' => $renderedPrompt['user_prompt'],
                'input_payload' => $renderPayload,
            ]);

            $strategyPlan = $organization->strategyPlans()->create([
                'prompt_template_id' => $promptTemplate->id,
                'period_type' => $attributes['period_type'],
                'title' => $attributes['title'] ?? null,
                'goals_json' => $goals,
                'input_payload' => $inputPayload,
                'output_text' => $response['output_text'] ?? null,
                'model_name' => $response['model_name'] ?? null,
                'provider_name' => $response['provider_name'] ?? null,
                'status' => 'completed',
            ]);

            $this->aiRequestLoggerService->log($organization, [
                'module' => 'plans',
                'task_type' => $attributes['period_type'],
                'provider_name' => $response['provider_name'] ?? null,
                'model_name' => $response['model_name'] ?? null,
                'prompt_snapshot' => $response['prompt_snapshot'] ?? null,
                'input_payload' => $this->buildLogInputPayload($promptTemplate, $attributes, $goals, $inputPayload),
                'output_payload' => $response['output_text'] ?? null,
                'tokens_input' => $response['tokens_input'] ?? null,
                'tokens_output' => $response['tokens_output'] ?? null,
                'estimated_cost' => $response['estimated_cost'] ?? null,
                'latency_ms' => $response['latency_ms'] ?? null,
                'status' => 'completed',
                'error_message' => null,
            ]);

            return $strategyPlan;
        } catch (Throwable $exception) {
            $this->aiRequestLoggerService->log($organization, [
                'module' => 'plans',
                'task_type' => $attributes['period_type'],
                'provider_name' => config('ai.default_provider'),
                'model_name' => config('ai.providers.openai.model'),
                'prompt_snapshot' => trim(
                    "system_prompt:\n" . ($renderedPrompt['system_prompt'] ?? '') . "\n\nuser_prompt:\n" . $renderedPrompt['user_prompt']
                ),
                'input_payload' => $this->buildLogInputPayload($promptTemplate, $attributes, $goals, $inputPayload),
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
     * @param  list<string>  $goals
     * @param  array<string, mixed>  $inputPayload
     * @return array<string, mixed>
     */
    private function buildLogInputPayload(PromptTemplate $promptTemplate, array $attributes, array $goals, array $inputPayload): array
    {
        return [
            'prompt_template_id' => $promptTemplate->id,
            'title' => $attributes['title'] ?? null,
            'period_type' => $attributes['period_type'],
            'goals' => $goals,
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
