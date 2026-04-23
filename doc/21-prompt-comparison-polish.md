# Lightweight Prompt Comparison Polish

## Scope

This step adds only lightweight prompt comparison polish for the existing prompt management flow.

It does not add a true diff engine, syntax highlighting, inline editing from compare screens, merge flows, conflict resolution, or any new AI generation modules.

## Compare Route

New route:

- `GET /prompt-templates/{promptTemplate}/compare`

The compare page uses query params:

- `from_version_id=`
- `to_version_id=`

Supported lightweight comparisons:

- snapshot to snapshot
- snapshot to current live template when one side is omitted

Example usage:

- compare snapshot to current live prompt:
    - `/prompt-templates/{promptTemplate}/compare?from_version_id=12`
- compare one snapshot to another snapshot:
    - `/prompt-templates/{promptTemplate}/compare?from_version_id=12&to_version_id=15`

## Compared Fields

The comparison page shows these fields only:

- title
- module
- description
- system prompt
- user prompt template

It also shows a lightweight label and timestamp for each side being compared.

## How Changes Are Indicated

This comparison remains intentionally simple.

There is no line-level diffing.

Instead, each field is marked by a basic equality check:

- `Changed` badge when the two values differ
- `Unchanged` badge when the values match
- subtle changed background styling for changed fields

This keeps the screen readable without building a dedicated diff system.

## Edit Page Helper

The prompt edit page now includes a compact `Compare to Current` link for each recent version.

This provides the most practical comparison path without adding a larger compare-selection workflow.

## Guardrails

Compared snapshot ids must belong to the selected prompt template.

If an invalid version id is provided for that prompt template, the compare page fails safely with `404`.

## Intentionally Postponed

Still out of scope after this step:

- true line-by-line diffing
- syntax highlighting
- merge tools
- compare-driven editing
- compare history dashboards
- prompt conflict resolution
- any new AI generation modules
