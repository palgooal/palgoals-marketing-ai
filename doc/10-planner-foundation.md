# Planner Foundation

## Scope

This step adds the first production-facing planner workflow and keeps the implementation intentionally narrow.

Included:

- `strategy_plans` persistence
- admin routes, controller, request validation, runner, and views for planner generation
- lightweight filtering and pagination on the plans index
- run-again flow that prefills the create form from an existing plan
- one default seeded planner prompt template using the existing `prompt_templates` table

Excluded for now:

- analyzer, chatbot, and other AI modules
- queues and background processing
- edit/delete flows for plans
- analytics, exports, advanced planning UI, and calendar UI
- a large shared generation framework

## Schema

The `strategy_plans` table stores generated planning drafts:

- `id`
- `organization_id`
- `prompt_template_id` nullable
- `period_type`
- `title` nullable
- `goals_json` nullable JSON array
- `input_payload` nullable JSON object
- `output_text` nullable long text
- `model_name` nullable
- `provider_name` nullable
- `status`
- timestamps

The corresponding `StrategyPlan` model belongs to `Organization` and optionally belongs to `PromptTemplate`.

## Workflow

Routes added:

- `GET /plans`
- `GET /plans/create`
- `POST /plans/generate`
- `GET /plans/{strategyPlan}`

The create form accepts:

- `prompt_template_id`
- `title`
- `period_type`
- `goals` entered one goal per line
- `input_payload` as a JSON object
- optional `context`

On submit, the workflow:

1. validates the request
2. loads the selected active prompt template
3. parses goals from one-per-line input into `goals_json`
4. renders prompt placeholders using the existing lightweight renderer
5. executes the request through the current AI execution stack
6. stores a `strategy_plans` record
7. redirects to a dedicated show page

## Goals Convention

Goals are entered as plain text with one goal per line. The controller converts that textarea into a trimmed array before persistence and generation.

## Filters And Pagination

The plans index supports lightweight filtering by:

- `period_type`
- `status`
- `prompt_template_id`
- title search

Pagination is set to 10 items per page and uses `withQueryString()` so active filters remain applied across pages.

## Run Again Behavior

The show page includes a `Run Again` action.

This links to `/plans/create?from={id}` and prefills the create form with:

- prompt template
- title
- period type
- goals converted back to one-per-line text
- optional context extracted from stored payload
- JSON payload preview without creating a new record until the form is submitted

## Seeded Prompt Template

The prompt template seeder now ensures one default active planner prompt exists:

- key: `plans.basic-strategy`
- title: `Basic Strategy Planner`
- module: `plans`

The seed logic is idempotent through `updateOrCreate`.

## Postponed Work

Still intentionally postponed after this step:

- analyzer foundation
- shared AI generation UX consolidation
- queued execution and retries
- advanced planning interfaces and calendar views
- edit/delete lifecycle and reporting for plans
