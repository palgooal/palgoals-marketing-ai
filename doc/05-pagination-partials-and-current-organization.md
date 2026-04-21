# Step 5: Pagination, Partials, and Current Organization

## Pagination behavior

- Added Laravel pagination to:
  - Services
  - Template Categories
  - Templates
  - Knowledge Documents
- Each list now shows `10` records per page
- Pagination links preserve active filter query parameters

## Current organization helper

- Added `App\Support\CurrentOrganization`
- The helper exposes `CurrentOrganization::get()`
- It currently resolves the active organization with `Organization::query()->firstOrFail()`
- Controllers updated in this step now use the helper instead of repeating direct lookups

## Form partial reuse

- Added local Blade partials for:
  - `services`
  - `template-categories`
  - `templates`
  - `knowledge-documents`
- Each resource now reuses its own partial in both create and edit pages
- The partials remain local to each resource and do not introduce a shared component layer

## Filter persistence behavior

- Existing GET filters remain populated after submit
- Pagination keeps the same filter values across pages via `withQueryString()`
- Reset links still return the list to its default unfiltered state

## Intentionally postponed

- Shared filter helpers across controllers
- Reusable table partials
- A global current-organization middleware or container binding
- AI core, prompts, logs, planner, offers, content generation, and analyzer features
