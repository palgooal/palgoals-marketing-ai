# AI UX Consolidation And Logs

## Scope

This step keeps the existing AI workflows intact and adds two narrow improvements:

- internal admin visibility for `ai_requests`
- small shared Blade partials that reduce repeated metadata, status, and JSON preview markup

## AI Logs Pages

New admin pages were added for `ai_requests`:

- `GET /logs/ai-requests`
- `GET /logs/ai-requests/{aiRequest}`

The logs index shows recent requests with:

- `module`
- `task_type`
- provider / model
- status
- latency
- created timestamp

The logs show page displays:

- organization
- module and task type
- provider and model
- tokens and estimated cost when present
- latency
- status
- error message when present
- prompt snapshot preview
- input payload preview
- output payload preview

## Filters And Pagination

The AI logs index supports lightweight filtering by:

- `module`
- `task_type`
- `status`
- `provider_name`
- optional search against module or task type

Pagination stays server-rendered and preserves filters with `withQueryString()`.

## Shared Partials

Three small Blade partials were introduced:

- `partials/ai/status-badge`
- `partials/ai/json-preview`
- `partials/ai/result-meta`

These are used to reduce repeated Blade markup across content, offers, plans, analysis, and AI logs pages.

## Navigation And Dashboard Polish

The admin sidebar now groups AI-related items more clearly:

- Prompt Templates
- Content
- Offers
- Plans
- Analysis
- AI Logs

The dashboard also includes a lightweight AI activity section with links to the latest content, offer, plan, analysis, and the AI logs page.

## Postponed Work

Still intentionally postponed after this step:

- queue jobs and background processing
- delete/export flows for logs
- advanced analytics, charting, or dashboards
- prompt governance and versioning workflows beyond the current starter setup
- broader UI abstraction layers or component libraries
