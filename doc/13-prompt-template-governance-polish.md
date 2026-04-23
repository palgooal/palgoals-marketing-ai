# Prompt Template Governance And Usability Polish

## Scope

This step stays intentionally narrow and improves prompt template management only.

It adds:

- a clearer prompt template index for day-to-day management
- a lightweight duplicate action for creating inactive drafts
- a simple activate/deactivate toggle
- tighter module and status validation guidance
- lightweight usage visibility on the edit screen
- clearer scope labels for global versus organization templates

## Prompt Template Index Polish

The prompt template index keeps the existing filters and pagination, and now shows:

- title
- key
- module
- version
- scope label
- active or inactive status
- updated timestamp
- management actions

Management actions remain intentionally lightweight:

- edit
- duplicate
- activate or deactivate

The empty state was also made clearer and includes a direct create action.

## Duplicate Action

New route:

- `POST /prompt-templates/{promptTemplate}/duplicate`

Behavior:

- copies the existing prompt template into a new record
- appends `Copy` to the title, with a numeric suffix if needed
- creates a simple unique key by appending `.copy`, with a numeric suffix if needed
- preserves the original scope (`organization_id` or global)
- resets `version` to `1`
- sets `is_active` to `false`
- redirects directly to the edit screen of the duplicate

This is intentionally a draft convenience feature, not a clone history system.

## Toggle Active Action

New route:

- `PATCH /prompt-templates/{promptTemplate}/toggle-active`

Behavior:

- flips `is_active`
- redirects back to the prompt template index with a small status message

No approval or publishing workflow is added in this step.

## Validation And Form Guidance

Prompt template validation remains simple and readable:

- `key` is required and unique
- `title` is required
- `module` is required and constrained to the current supported workflow modules
- `version` must be an integer with a minimum value of `1`
- `is_active` is treated as a boolean

The create and edit forms now clarify:

- the difference between `system_prompt` and `user_prompt_template`
- supported placeholders such as `{{title}}`, `{{type}}`, `{{language}}`, and `{{tone}}`
- that some workflows may pass extra payload or context fields

## Lightweight Usage Visibility

The edit screen now shows simple related usage counts for:

- content generations
- offer generations
- strategy plans
- page analyses

This is intentionally limited to direct relationship counts. No analytics layer or historical dashboard is introduced.

## Scope Labeling

Prompt templates now display a lightweight scope label in the UI:

- `Global` when `organization_id` is `null`
- `Organization` when `organization_id` is present

This step keeps scope display-only. It does not add multi-organization scope management.

## Intentionally Postponed

Still out of scope after this step:

- full prompt version history
- approval or publishing workflows
- restore, archive, or delete governance flows
- clone lineage tracking
- permissions matrix changes
- prompt analytics dashboards
- new AI generation modules
