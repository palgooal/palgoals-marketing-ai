# Step 4: Knowledge Documents and Light Polish

## New table added

- `knowledge_documents`

## New CRUD added

- `KnowledgeDocument`
- Index, create, store, edit, and update screens
- New sidebar entry: `Knowledge Documents`

## Filter behavior

- Services:
  - `search` filters by title
  - `status` filters by `draft`, `active`, or `archived`
- Template Categories:
  - `search` filters by name
  - `status` filters by `active` or `inactive`
- Templates:
  - `search` filters by name
  - `status` filters by `draft`, `active`, or `archived`
- Knowledge Documents:
  - `search` filters by title
  - `activity` filters by active or inactive state

## Metadata key:value textarea convention

- Knowledge document metadata is entered as one `key:value` pair per line
- Empty lines are ignored
- Values are normalized into an associative array before saving
- If a line has no value after the colon, the key is still stored with an empty string

## Status options used

- Services:
  - `draft`
  - `active`
  - `archived`
- Template Categories:
  - `active`
  - `inactive`
- Templates:
  - `draft`
  - `active`
  - `archived`

## Intentionally postponed

- Delete actions
- Pagination
- Reusable CRUD form partials
- Central current-organization resolver/helper
- AI generation, prompts, logs, planner, analyzer, and offers
