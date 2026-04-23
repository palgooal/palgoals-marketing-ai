# Review Approval Lite

## Scope

This step adds a lightweight review and approval layer for existing generated outputs only.

It does not add a workflow engine. It only standardizes statuses, exposes small actions on the existing show pages, and adds a small dashboard summary.

## Normalized Output Statuses

Generated outputs now use a shared lightweight vocabulary:

- `draft`
- `reviewed`
- `approved`

Successful newly generated outputs now start as `draft`.

For legacy records that still store `completed`, the UI and filtering normalize them as `draft` at the application layer. No migration was added for this step.

## Review And Approval Actions

Each existing generated-output module now has two small actions:

- `PATCH .../{record}/mark-reviewed`
- `PATCH .../{record}/mark-approved`

These were added for:

- content generations
- offer generations
- strategy plans
- page analyses

Behavior stays intentionally narrow:

- update the record `status` only
- redirect back to the existing show page
- show a small flash message

No approval roles matrix, comments, notifications, or workflow history were introduced.

## Show Page UI

The existing show pages now:

- display the current review status clearly
- reuse the shared AI status badge partial
- show `Mark as Reviewed` when the output is still effectively a draft
- show `Mark as Approved` when the output is not already approved

This keeps the UI small and practical without redesigning the pages.

## Index Filtering

The existing index filters for content, offers, plans, and analysis now align to the normalized values:

- `draft`
- `reviewed`
- `approved`

Legacy `completed` records are treated as `draft` for filtering so older data still appears correctly.

## Dashboard Summary

The dashboard now includes a small server-rendered review summary showing counts for:

- draft outputs
- reviewed outputs
- approved outputs

The counts aggregate across:

- content
- offers
- plans
- analysis

No charts or analytics layer were added.

## Intentionally Postponed

Still out of scope after this step:

- comments or review notes
- approval roles matrix
- notifications
- activity timelines
- full editorial workflow states
- delete or archive flows
- export/publish pipelines
- new AI generation modules
