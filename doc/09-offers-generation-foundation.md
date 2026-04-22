# Offers Generation Foundation

## Scope

This step adds the first production-facing offers generation workflow and keeps the implementation intentionally narrow.

Included:

- `offer_generations` persistence
- admin routes, controller, request validation, and views for offers generation
- lightweight filtering and pagination on the offers index
- run-again flow that prefills the create form from an existing offer generation
- one default seeded offer prompt template using the existing `prompt_templates` table

Excluded for now:

- planner, analyzer, chatbot, or other AI modules
- queues and background processing
- edit/delete flows for offers
- analytics, exports, and advanced orchestration
- a large shared generation framework beyond the small dedicated offers runner

## Schema

The `offer_generations` table stores generated offer drafts:

- `id`
- `organization_id`
- `prompt_template_id` nullable
- `title` nullable
- `offer_type`
- `input_payload` nullable JSON object
- `output_text` nullable long text
- `model_name` nullable
- `provider_name` nullable
- `status`
- timestamps

The corresponding `OfferGeneration` model belongs to `Organization` and optionally belongs to `PromptTemplate`.

## Workflow

Routes added:

- `GET /offers`
- `GET /offers/create`
- `POST /offers/generate`
- `GET /offers/{offerGeneration}`

The create form accepts:

- `prompt_template_id`
- `title`
- `offer_type`
- `input_payload` as a JSON object
- optional `context`

On submit, the workflow:

1. validates the request
2. loads the selected active prompt template
3. renders prompt placeholders using the existing lightweight renderer
4. executes the request through the current AI execution stack
5. stores an `offer_generations` record
6. redirects to a dedicated show page

## Filters And Pagination

The offers index supports lightweight filtering by:

- `offer_type`
- `status`
- `prompt_template_id`
- title search

Pagination is set to 10 items per page and uses `withQueryString()` so active filters remain applied across pages.

## Run Again Behavior

The show page includes a `Run Again` action.

This links to `/offers/create?from={id}` and prefills the create form with:

- prompt template
- title
- offer type
- optional context extracted from stored payload
- JSON payload preview without creating a new record until the form is submitted

## Seeded Prompt Template

The prompt template seeder now ensures one default active offer prompt exists:

- key: `offers.basic-offer`
- title: `Basic Offer Generator`
- module: `offers`

The seed logic is idempotent through `updateOrCreate`.

## Postponed Work

Still intentionally postponed after this step:

- planner foundation
- more advanced shared AI generation UX
- queued execution and retries
- richer prompt-template scoping rules per module
- edit/delete lifecycle and reporting for offers
