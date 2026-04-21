<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AIExecutionService;
use App\Services\AI\AIRequestLoggerService;
use App\Support\CurrentOrganization;
use Illuminate\Http\Request;
use JsonException;
use Throwable;
use Illuminate\View\View;

class AiTestController extends Controller
{
    public function __construct(
        private readonly AIExecutionService $aiExecutionService,
        private readonly AIRequestLoggerService $aiRequestLoggerService,
    ) {}

    public function show(): View
    {
        return view('ai.test', $this->viewData(
            module: 'internal',
            taskType: 'test',
            payloadText: "{\n    \"message\": \"Hello from Palgoals Marketing AI\"\n}",
        ));
    }

    public function store(Request $request): View
    {
        $validated = $request->validate([
            'module' => ['required', 'string', 'max:100'],
            'task_type' => ['required', 'string', 'max:100'],
            'payload' => ['required', 'string'],
        ]);

        $organization = CurrentOrganization::get();

        try {
            $payload = json_decode($validated['payload'], true, 512, JSON_THROW_ON_ERROR);

            if (! is_array($payload)) {
                throw new JsonException('Payload JSON must decode to an object or array.');
            }

            $response = $this->aiExecutionService->execute(
                $validated['module'],
                $validated['task_type'],
                $payload,
            );

            $loggedRequest = $this->aiRequestLoggerService->log($organization, [
                'module' => $validated['module'],
                'task_type' => $validated['task_type'],
                'provider_name' => (string) ($response['provider_name'] ?? config('ai.default_provider')),
                'model_name' => (string) ($response['model_name'] ?? config('ai.providers.openai.model')),
                'prompt_snapshot' => $response['prompt_snapshot'] ?? null,
                'input_payload' => $payload,
                'output_payload' => $response['output_text'] ?? null,
                'tokens_input' => $response['tokens_input'] ?? null,
                'tokens_output' => $response['tokens_output'] ?? null,
                'estimated_cost' => $response['estimated_cost'] ?? null,
                'latency_ms' => $response['latency_ms'] ?? null,
                'status' => 'completed',
                'error_message' => null,
            ]);

            return view('ai.test', $this->viewData(
                module: $validated['module'],
                taskType: $validated['task_type'],
                payloadText: $validated['payload'],
                response: $response,
                loggedRequestId: $loggedRequest?->id,
            ));
        } catch (Throwable $exception) {
            $this->aiRequestLoggerService->log($organization, [
                'module' => $validated['module'],
                'task_type' => $validated['task_type'],
                'provider_name' => (string) config('ai.default_provider'),
                'model_name' => (string) config('ai.providers.openai.model'),
                'prompt_snapshot' => $validated['payload'],
                'input_payload' => ['raw' => $validated['payload']],
                'output_payload' => null,
                'tokens_input' => null,
                'tokens_output' => null,
                'estimated_cost' => null,
                'latency_ms' => null,
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            return view('ai.test', $this->viewData(
                module: $validated['module'],
                taskType: $validated['task_type'],
                payloadText: $validated['payload'],
                error: $exception->getMessage(),
            ));
        }
    }

    /**
     * @param  array<string, mixed>|null  $response
     * @return array<string, mixed>
     */
    private function viewData(
        string $module,
        string $taskType,
        string $payloadText,
        ?array $response = null,
        ?string $error = null,
        ?int $loggedRequestId = null,
    ): array {
        return [
            'module' => $module,
            'taskType' => $taskType,
            'payloadText' => $payloadText,
            'response' => $response,
            'error' => $error,
            'loggedRequestId' => $loggedRequestId,
        ];
    }
}
