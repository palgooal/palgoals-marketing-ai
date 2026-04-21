# Step 6: AI Core Foundation

## Config added

- Added `config/ai.php`
- Current keys:
  - `default_provider`
  - `providers.openai.model`
  - `providers.openai.embedding_model`
  - `request_logging.enabled`

## ai_requests schema

- Added `ai_requests` table
- Fields include:
  - organization, module, task type
  - provider/model names
  - prompt snapshot
  - input and output payloads
  - token counts
  - estimated cost
  - latency
  - status and error message

## Provider contract structure

- `App\Services\AI\Contracts\AIProviderInterface`
- `App\Services\AI\Providers\OpenAIProvider`
- The provider contract currently exposes one method:
  - `execute(string $module, string $taskType, array $payload): array`

## Router, logger, and execution roles

- `AIModelRouterService`
  - currently routes everything to the configured OpenAI provider/model
- `AIExecutionService`
  - resolves the provider
  - executes the request
  - returns a normalized response structure
- `AIRequestLoggerService`
  - persists AI request records into `ai_requests`
  - honors `ai.request_logging.enabled`

## AI test screen purpose

- Added authenticated internal test screen at `/ai/test`
- It is intended only for admin/developer verification of the foundation
- The screen accepts:
  - `module`
  - `task_type`
  - JSON `payload`
- On submit it:
  - parses payload JSON
  - executes through `AIExecutionService`
  - logs the request
  - shows normalized output or a friendly error

## Intentionally postponed

- Prompt templates
- Content generation workflows
- AI request history UI
- Multiple provider execution paths beyond the initial OpenAI foundation
- Queues, jobs, retries, and batching
- Planner, offers, analyzer, and other generation modules
