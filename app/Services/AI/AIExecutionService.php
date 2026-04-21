<?php

namespace App\Services\AI;

use App\Services\AI\Providers\OpenAIProvider;
use RuntimeException;

class AIExecutionService
{
    public function __construct(
        private readonly AIModelRouterService $modelRouter,
        private readonly OpenAIProvider $openAIProvider,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function execute(string $module, string $taskType, array $payload): array
    {
        $route = $this->modelRouter->route($module, $taskType, $payload);
        $startedAt = microtime(true);

        $result = match ($route['provider_name']) {
            'openai' => $this->openAIProvider->execute($module, $taskType, $payload),
            default => throw new RuntimeException("Unsupported AI provider [{$route['provider_name']}]."),
        };

        $result['provider_name'] ??= $route['provider_name'];
        $result['model_name'] ??= $route['model_name'];
        $result['prompt_snapshot'] ??= $this->buildPromptSnapshot($module, $taskType, $payload);
        $result['latency_ms'] ??= (int) round((microtime(true) - $startedAt) * 1000);

        return $result;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function buildPromptSnapshot(string $module, string $taskType, array $payload): string
    {
        return implode(PHP_EOL, [
            "module: {$module}",
            "task_type: {$taskType}",
            'payload:',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);
    }
}
