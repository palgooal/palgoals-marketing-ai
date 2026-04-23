# Prompt History And Revert Polish

## Scope

This step adds only lightweight prompt history preview and revert polish to the existing prompt template edit flow.

It does not add a full history browser, diff tooling, side-by-side compare views, or any approval workflow for prompt changes.

## Recent Versions On The Edit Page

Recent prompt versions continue to appear directly on the existing prompt template edit page.

The panel now shows a compact history row for each recent snapshot including:

- `version_number`
- snapshot timestamp
- title snapshot
- lightweight preview action
- lightweight revert action

This keeps history access close to the existing edit workflow without adding a separate index page.

## Version Preview

Each recent version now supports a lightweight preview route:

- `GET /prompt-templates/{promptTemplate}/versions/{promptTemplateVersion}`

The preview screen shows the snapshot fields clearly:

- `version_number`
- `title`
- `description`
- `module`
- `system_prompt`
- `user_prompt_template`

The UI remains simple Blade and follows the existing authenticated admin flow.

## Revert Action

Each recent version also supports a lightweight revert action:

- `POST /prompt-templates/{promptTemplate}/versions/{promptTemplateVersion}/revert`

Revert behavior is intentionally simple:

- the current live prompt template state is snapshotted first
- the selected snapshot content is copied back onto the live prompt template
- the live prompt template `version` is incremented by `1`
- the live prompt template database row is not replaced

Fields restored from the snapshot:

- `title`
- `description`
- `system_prompt`
- `user_prompt_template`
- `module`

Fields intentionally preserved on the live prompt template:

- `key`
- `organization_id`
- `is_active`

## Version Increment Behavior On Revert

Revert uses the same lightweight versioning pattern as normal prompt updates.

That means:

- the pre-revert live state is stored as a new version snapshot using the live prompt's current version number
- the reverted live prompt then moves forward to the next version number

Example:

- live prompt is version `5`
- reverting to snapshot version `2`
- current live version `5` is snapshotted first
- live prompt becomes the selected snapshot content at version `6`

## Guardrails

Basic safety checks were added:

- the selected version must belong to the selected prompt template
- preview and revert both fail safely with a not-found response if the version does not belong to that prompt template

Authorization remains the same authenticated admin-only flow used by the rest of the internal prompt management UI.

## Intentionally Postponed

Still out of scope after this step:

- full prompt history browser
- diff engine
- side-by-side comparison
- approval flow for prompt changes
- prompt publishing workflow
- bulk restore tools
- branching or draft lineage for prompt versions
