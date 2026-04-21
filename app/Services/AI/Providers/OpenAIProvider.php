<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use OpenAI\Laravel\Facades\OpenAI;
use RuntimeException;

class OpenAIProvider implements AIProviderInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function execute(string $module, string $taskType, array $payload): array
    {
        if (blank(config('openai.api_key'))) {
            throw new RuntimeException('OpenAI API key is not configured. Set OPENAI_API_KEY before using the AI test screen.');
        }

        $systemPrompt = $payload['system_prompt'] ?? 'You are the internal Palgoals Marketing AI foundation endpoint. Respond concisely and clearly.';
        $userPrompt = $payload['user_prompt'] ?? json_encode([
            'module' => $module,
            'task_type' => $taskType,
            'payload' => $payload,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $response = OpenAI::chat()->create([
            'model' => (string) config('ai.providers.openai.model'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => $userPrompt,
                ],
            ],
        ]);

        return [
            'provider_name' => 'openai',
            'model_name' => $response->model,
            'output_text' => $response->choices[0]->message->content ?? '',
            'tokens_input' => $response->usage?->promptTokens,
            'tokens_output' => $response->usage?->completionTokens,
            'estimated_cost' => null,
            'prompt_snapshot' => trim(
                "system_prompt:\n{$systemPrompt}\n\nuser_prompt:\n{$userPrompt}"
            ),
            'raw_response' => $response->toArray(),
        ];
    }
}
