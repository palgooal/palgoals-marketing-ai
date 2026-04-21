# Step 2: Admin Layout and Settings

## What was added

- A reusable authenticated admin layout for internal dashboard pages
- A lightweight left sidebar with Dashboard, Brand Profile, and Settings links
- A simple settings page backed by the existing `settings` table
- Dashboard polish with summary cards and quick links
- Existing dashboard and brand profile pages refactored to use the new admin shell

## New routes

- `GET /dashboard` named `dashboard`
- `GET /brand-profile` named `brand.edit`
- `PUT /brand-profile` named `brand.update`
- `GET /settings` named `settings.edit`
- `PUT /settings` named `settings.update`

## Layout structure

- `resources/views/layouts/admin.blade.php`
- Left sidebar for primary internal navigation
- Top header for the project title and authenticated user actions
- Main content area for dashboard, brand, and settings screens

## Settings keys used

- `app_name`
- `support_email`
- `default_primary_language`
- `default_secondary_language`

## Deferred improvements

- Collapsible mobile sidebar behavior
- Additional settings sections
- Permission-based navigation visibility
- Dedicated form components for the admin shell
