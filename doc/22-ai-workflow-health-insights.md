# Internal AI Workflow Health Insights

## Scope

This step adds only lightweight internal AI workflow health insights.

It does not add charts, queues, retry execution, notifications, heavy analytics, or any new AI generation modules.

## Dashboard Health Counts

The dashboard now includes a small `Workflow Health` section with compact server-rendered counts for:

- failed AI requests
- successful AI requests
- draft outputs
- reviewed outputs
- approved outputs
- published outputs
- unpublished outputs

The counts aggregate across the existing internal workflow records:

- content
- offers
- plans
- analysis
- `ai_requests` where relevant

## Stale Draft Definition

The dashboard also includes a `Stale Drafts` subsection.

Definition used in this step:

- outputs still in draft status
- created more than `7` days ago

The stale draft summary shows:

- one aggregate count
- small links back to the relevant module indexes when stale drafts exist

The threshold remains intentionally simple and is hardcoded in a tiny helper.

## AI Logs Health Indicators

The existing AI logs index now has a lightweight `Health` filter with:

- `Failed Only`
- `Slow Requests`
- `Missing Output / Error`

It also shows small row-level health badges when applicable:

- `Failed`
- `Slow`
- `Missing Output / Error`

`Slow` in this step means:

- `latency_ms >= 1000`

This remains fully server-rendered.

## Prompt Health Hint

Prompt health stays lightweight.

Two small hints were added:

- dashboard card for `Unused Active Prompts`
- prompt templates index badge for `Unused Active Prompt`

In this step, that means a prompt template is:

- active
- has zero usage across content, offers, plans, and analysis

## Tiny Helper

A very small helper was added:

- `App\Support\AIWorkflowHealthInsights`

Responsibilities:

- calculate dashboard health counts
- calculate stale draft summary
- expose the stale draft and slow-request thresholds
- support simple AI log health labeling checks

It is intentionally not a reporting engine.

## Intentionally Postponed

Still out of scope after this step:

- charts
- background aggregation jobs
- notifications
- retry execution engine
- complex drilldown pages
- historical trend analysis
- cross-module reporting dashboards
- new AI generation modules