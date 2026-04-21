<?php

namespace App\Services\AI;

class AIModelRouterService
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{provider_name: string, model_name: string}
     */
    public function route(string $module, string $taskType, array $payload): array
    {
        return [
            'provider_name' => (string) config('ai.default_provider', 'openai'),
            'model_name' => (string) config('ai.providers.openai.model'),
        ];
    }
}
