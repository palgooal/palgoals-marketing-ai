# Internal Content Package And Export Improvements

## Scope

This step adds only lightweight internal package views and richer package text exports for existing generated outputs.

It does not add PDF export, DOCX export, email/send flows, public share pages, zip bundles, or any new AI generation modules.

## Package Views Added

Each existing generated-output module now has an internal package view route:

- `GET /content/{contentGeneration}/package`
- `GET /offers/{offerGeneration}/package`
- `GET /plans/{strategyPlan}/package`
- `GET /analysis/{pageAnalysis}/package`

These package pages are internal admin-facing handoff views rendered with simple Blade templates.

Each package view includes:

- title or primary label
- type, offer type, period type, or page type
- review status
- published state
- provider and model
- prompt template used
- main output text
- input payload preview
- stored context details when available

Plans also surface goals clearly in the package view.

Analysis packages show combined findings and recommendations in a simpler internal handoff format.

## Package Text Export Routes

Each module now also supports a richer plain text package export route:

- `GET /content/{contentGeneration}/export-package-text`
- `GET /offers/{offerGeneration}/export-package-text`
- `GET /plans/{strategyPlan}/export-package-text`
- `GET /analysis/{pageAnalysis}/export-package-text`

These responses return `text/plain` and are intended for internal handoff or copy/export workflows.

## Tiny Helper Added

A small helper was introduced:

- `App\Support\AIPackageTextFormatter`

Its responsibility is intentionally narrow:

- format richer internal package text exports
- include richer metadata than the existing compact text export
- include context details and input payload text where useful

This is not a generic export framework.

## Shared Partials Added

Small shared Blade partials were added for package pages:

- package header meta partial
- package section partial

These are intentionally lightweight and only reduce obvious duplication across the four package pages.

## Difference Between Export Text And Export Package Text

Existing `export-text` behavior remains the compact plain text export.

The new `export-package-text` behavior is richer and more handoff-oriented.

`export-text` focuses on:

- compact metadata
- primary output only

`export-package-text` adds more internal handoff detail such as:

- published state
- prompt template information
- context details
- richer output packaging
- input payload section

## Navigation Polish

Existing show pages for content, offers, plans, and analysis now include:

- `View Package`
- `Export Package Text`

Existing index tables now include a lightweight `Package` action link while keeping the previous actions intact.

## Intentionally Postponed

Still out of scope after this step:

- PDF export
- DOCX export
- public package links
- email or send flows
- zip bundles
- asset packaging
- branded external handoff pages
- queue-based export jobs
- new AI generation modules
