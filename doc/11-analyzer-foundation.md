# Analyzer Foundation

## Scope

This step adds the first production-facing analyzer workflow and keeps the implementation intentionally narrow.

Included:

- `page_analyses` persistence
- admin routes, controller, request validation, runner, and views for page analysis
- lightweight filtering and pagination on the analysis index
- run-again flow that prefills the create form from an existing analysis
- one default seeded analyzer prompt template using the existing `prompt_templates` table

Excluded for now:

- chatbot and other AI modules
- queues and background processing
- edit/delete flows for analysis
- exports, crawling, automatic HTML fetching, advanced scoring, and complex parsing
- a large shared generation framework

## Schema

The `page_analyses` table stores generated page reviews:

- `id`
- `organization_id`
- `prompt_template_id` nullable
- `page_title` nullable
- `page_url` nullable
- `page_type` nullable
- `input_payload` nullable JSON object
- `findings_text` nullable long text
- `recommendations_text` nullable long text
- `score` nullable integer
- `model_name` nullable
- `provider_name` nullable
- `status`
- timestamps

The corresponding `PageAnalysis` model belongs to `Organization` and optionally belongs to `PromptTemplate`.

## Workflow

Routes added:

- `GET /analysis`
- `GET /analysis/create`
- `POST /analysis/run`
- `GET /analysis/{pageAnalysis}`

The create form accepts:

- `prompt_template_id`
- `page_title`
- `page_url`
- `page_type`
- `input_payload` as a JSON object
- optional `page_content`
- optional `context`

On submit, the workflow:

1. validates the request
2. loads the selected active prompt template
3. renders prompt placeholders using the existing lightweight renderer
4. executes the request through the current AI execution stack
5. stores a `page_analyses` record
6. redirects to a dedicated show page

## Findings And Recommendations Storage

For this first step, output parsing stays intentionally minimal.

- `recommendations_text` stores the full model output
- `findings_text` stays nullable unless a later step adds a clearer parsing convention
- `score` stays nullable unless a simple extraction rule becomes necessary

## Filters And Pagination

The analysis index supports lightweight filtering by:

- `page_type`
- `status`
- `prompt_template_id`
- page title search

Pagination is set to 10 items per page and uses `withQueryString()` so active filters remain applied across pages.

## Run Again Behavior

The show page includes a `Run Again` action.

This links to `/analysis/create?from={id}` and prefills the create form with:

- prompt template
- page title
- page URL
- page type
- page content and context extracted from stored payload
- JSON payload preview without creating a new record until the form is submitted

## Seeded Prompt Template

The prompt template seeder now ensures one default active analyzer prompt exists:

- key: `analysis.basic-page-review`
- title: `Basic Page Analyzer`
- module: `analysis`

The seed logic is idempotent through `updateOrCreate`.

## Postponed Work

Still intentionally postponed after this step:

- shared AI generation UX consolidation
- internal logs visibility improvements
- queued execution and retries
- automatic crawling or fetching of live page HTML
- advanced scoring and structured parsers
