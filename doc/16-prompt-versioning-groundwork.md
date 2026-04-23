# Prompt Versioning Groundwork

## Scope

This step adds only lightweight prompt versioning groundwork.

It introduces a snapshot table, automatic version snapshots on update, and a compact recent versions section on the edit page.

It does not add revert, diffing, or a full history browser.

## Prompt Template Versions Schema

New table:

- `prompt_template_versions`

Stored fields:

- `prompt_template_id`
- `version_number`
- `title`
- `description`
- `system_prompt`
- `user_prompt_template`
- `module`
- `is_active`
- timestamps

Each record is a simple snapshot of an earlier prompt template state.

## Snapshot Behavior On Update

When an existing prompt template is updated through the current edit flow:

- the current prompt template state is first saved into `prompt_template_versions`
- the live `prompt_templates` record is then updated with the new submitted content

The snapshot stores the prompt template state from before the save.

No version snapshot is created on initial create in this step.

## Version Increment Behavior

The existing `prompt_templates.version` field remains the active version number.

On each prompt template update:

- the old state is snapshotted with its current `version_number`
- the live `prompt_templates.version` is incremented by `1`

This step keeps versioning intentionally simple:

- no diffing
- no semantic versioning
- no restore or revert logic

## Edit Page Visibility

The prompt template edit page now shows:

- the current live version number
- a compact recent versions section

The recent versions list includes:

- `version_number`
- `updated_at`
- a simple active or inactive snapshot label

This is intentionally only a lightweight visibility improvement, not a dedicated history page.

## Duplicate And Toggle Behavior

Existing duplicate and toggle-active actions continue to behave simply:

- duplicate creates a new draft prompt template starting at version `1`
- toggle-active still updates the live prompt template only

No version activation flow was added.

## Intentionally Postponed

Still out of scope after this step:

- full history browsing
- revert or restore actions
- compare or diff views
- version-specific activation
- publishing or approval engines for prompts
- archive flows
