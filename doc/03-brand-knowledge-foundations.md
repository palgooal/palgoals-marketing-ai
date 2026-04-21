# Step 3: Brand Knowledge Foundations

## Tables added

- `brand_services`
- `template_categories`
- `templates`

## Routes added

- `GET /services` named `services.index`
- `GET /services/create` named `services.create`
- `POST /services` named `services.store`
- `GET /services/{brandService}/edit` named `services.edit`
- `PUT /services/{brandService}` named `services.update`
- `GET /template-categories` named `template-categories.index`
- `GET /template-categories/create` named `template-categories.create`
- `POST /template-categories` named `template-categories.store`
- `GET /template-categories/{templateCategory}/edit` named `template-categories.edit`
- `PUT /template-categories/{templateCategory}` named `template-categories.update`
- `GET /templates` named `templates.index`
- `GET /templates/create` named `templates.create`
- `POST /templates` named `templates.store`
- `GET /templates/{template}/edit` named `templates.edit`
- `PUT /templates/{template}` named `templates.update`

## CRUD resources added

- `BrandService`
- `TemplateCategory`
- `Template`

## Sidebar updates

- Services
- Template Categories
- Templates

## Textarea to array convention

- JSON array fields are entered as one item per line in a textarea
- Blank lines are ignored
- Empty textarea values are stored as `null`
- The controller normalizes those textarea values into PHP arrays before saving

## Intentionally postponed

- Delete actions
- Knowledge documents module
- Filtering and search UI
- Status badges and richer status workflows
- AI-driven content or analysis features
