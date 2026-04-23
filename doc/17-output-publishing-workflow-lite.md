# Output Publishing Workflow Lite

## Scope

This step adds only a lightweight internal publishing workflow for existing generated outputs.

It does not add public pages, share links, scheduled publishing, notifications, queues, or new AI generation modules.

## Publishing Fields

The following generated-output tables now include lightweight publishing metadata:

- `content_generations`
- `offer_generations`
- `strategy_plans`
- `page_analyses`

New fields on each table:

- `is_published` boolean default `false`
- `published_at` nullable timestamp

These fields are also available on the matching Eloquent models:

- `ContentGeneration`
- `OfferGeneration`
- `StrategyPlan`
- `PageAnalysis`

## Publish And Unpublish Actions

Each existing generated-output module now has two lightweight actions:

- `PATCH .../{record}/publish`
- `PATCH .../{record}/unpublish`

Supported modules:

- content
- offers
- plans
- analysis

## Publish Rule

Publishing remains intentionally simple.

An output can be published only when its review status is already:

- `reviewed`
- `approved`

Draft outputs cannot be published.

If a publish action is attempted while the output is still draft, the user is redirected back with a friendly error flash.

There is no role-based publishing permission matrix in this step.

## Publish Behavior

When publishing an eligible output:

- `is_published` is set to `true`
- `published_at` is set to `now()` only if it has not already been set

This keeps the first publish timestamp stable across future unpublish and republish actions.

## Unpublish Behavior

When unpublishing an output:

- `is_published` is set to `false`
- `published_at` is kept as-is

This step intentionally preserves the last known publish timestamp instead of clearing it.

## Index Pages

The following index pages now include lightweight publishing polish:

- content index
- offers index
- plans index
- analysis index

Each index now shows:

- a published or unpublished indicator column
- a lightweight `published` filter
- pagination with query strings preserved

## Show Pages

Each generated-output show page now displays:

- review status
- publishing status
- publish or unpublish action button when applicable
- published timestamp when available

The UI remains internal, small, and admin-focused.

## Dashboard Summary

The dashboard now includes a small publishing summary section showing aggregate counts of:

- published outputs
- unpublished outputs

The counts are aggregated across:

- content generations
- offer generations
- strategy plans
- page analyses

## Intentionally Postponed

Still out of scope after this step:

- public share pages
- public URLs or share links
- scheduled publishing
- notifications
- queues
- role matrix for publishing
- archive workflows
- publishing audit trails
- publishing APIs
