# Lightweight Prompt Usage Insights

## Scope

This step adds only lightweight prompt usage insights on the existing prompt management and dashboard surfaces.

It does not add analytics pipelines, usage charts, prompt A/B testing, background aggregation jobs, event tracking, or any new AI generation modules.

## Prompt Index Improvements

The prompt templates index now includes compact usage visibility for each prompt template.

Each row now shows:

- total usage count
- content usage count
- offers usage count
- plans usage count
- analysis usage count

The existing filters now also include a lightweight usage filter:

- `Used`
- `Unused`

This filter is query-based and relies on existing prompt-template relationships only.

## Prompt Edit Insights

The prompt template edit page now includes a small usage summary section.

This section shows:

- total usage across all existing AI output modules
- per-module usage totals
- last used timestamp when available

The edit page also includes a compact recent usage list.

This list is intentionally lightweight and shows the most recent linked item for each existing module when available:

- content
- offers
- plans
- analysis

Each row links back to the existing generated output detail page.

This is not a full audit timeline.

## Dashboard Addition

The dashboard now includes a small `Prompt Activity` card.

It summarizes:

- total accessible prompt templates
- active accessible prompt templates
- unused accessible prompt templates

The card also links back to the prompt template index for follow-up management.

## Intentionally Postponed

Still out of scope after this step:

- time-series prompt analytics
- usage charts or trend graphs
- prompt performance scoring
- prompt-to-output quality comparison
- event-level audit feeds
- scheduled reporting
- external analytics sinks
- new AI generation modules
