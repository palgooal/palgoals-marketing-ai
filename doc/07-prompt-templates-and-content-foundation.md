# Step 7: Prompt Templates and Content Foundation

## prompt_templates schema

- `organization_id` nullable for future shared/global prompts
- `key` unique
- `title`
- `description`
- `system_prompt`
- `user_prompt_template`
- `module`
- `version`
- `is_active`

## content_generations schema

- `organization_id`
- `prompt_template_id` nullable
- `type`
- `title`
- `input_payload`
- `output_text`
- `language`
- `tone`
- `model_name`
- `provider_name`
- `status`

## First content workflow

- Admin creates or edits prompt templates
- Admin opens `/content/create`
- Selects one active prompt template
- Provides content inputs and a JSON payload
- The prompt is rendered and sent through the existing AI execution stack
- The generated output is stored in `content_generations`
- The AI request is also logged in `ai_requests`

## Placeholder behavior

- The first placeholder pass supports simple replacements such as:
  - `{{title}}`
  - `{{type}}`
  - `{{language}}`
  - `{{tone}}`
- Scalar keys from `input_payload` are also available as direct placeholders
- The full input payload is appended to the rendered user prompt as formatted JSON

## Seeded default prompt template

- `key`: `content.basic-marketing`
- `title`: `Basic Marketing Content`
- `module`: `content`
- Active by default
- Intended as the starter template for the first content generation workflow

## Intentionally postponed

- Prompt template delete flow
- Prompt version history beyond the current `version` field
- Content workflow filtering and UX polish
- Queues and background generation
- Offers, planner, analyzer, chatbot, and other AI modules
