# Export And Copy Usability Polish

## Scope

This step stays intentionally narrow and improves export and copy usability for existing generated outputs only.

It does not add file generation, sharing, attachments, or new output modules.

## Copy Actions Added

The existing show pages for content, offers, plans, and analysis now include lightweight copy actions for:

- the main generated output text
- the record title when present
- the input payload JSON
- the page URL on analysis records

The main output blocks were also made more clearly copy-ready in the UI by pairing the rendered text with visible copy buttons.

## Plain Text Export Routes

Each existing generated-output module now has a plain text export route:

- `GET /content/{contentGeneration}/export-text`
- `GET /offers/{offerGeneration}/export-text`
- `GET /plans/{strategyPlan}/export-text`
- `GET /analysis/{pageAnalysis}/export-text`

Each route returns a `text/plain` response with a compact readable structure including:

- title or record label
- type, offer type, period type, or page type
- normalized status
- provider and model when present
- generated output text

## Helper Introduced

A very small helper was added:

- `app/Support/AITextExportFormatter.php`

Its job is only to assemble readable plain text export output for the four existing generated-output record types.

This is intentionally not a general export framework.

## Index Usability

The existing index pages for content, offers, plans, and analysis now include:

- `View`
- `Export Text`

No broader table redesign was introduced.

## Intentionally Postponed

Still out of scope after this step:

- PDF export
- DOCX export
- CSV export
- email sending
- share links
- attachments
- archive systems
- new AI generation modules
