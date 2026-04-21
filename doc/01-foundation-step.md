# Step 1: Foundation

## What was added

- Laravel Breeze Blade authentication is used as the starting admin stack already present in the repository.
- Spatie `laravel-permission` is enabled for role-based access control.
- Core foundation data models were added for organizations, brand profiles, and application settings.
- A minimal authenticated dashboard and brand profile edit screen were added.
- Seeders were added to bootstrap the first internal Palgoals organization and admin account.

## Packages and setup

- `laravel/breeze`
- `spatie/laravel-permission`
- Existing repository setup already includes:
  - Breeze Blade auth routes, controllers, views, and layout
  - `config/permission.php`
  - Spatie permission migration

## Models and tables created

### Models

- `App\Models\Organization`
- `App\Models\BrandProfile`
- `App\Models\Setting`

### Tables

- `organizations`
- `brand_profiles`
- `settings`

### Relationships

- `Organization` has one `BrandProfile`
- `BrandProfile` belongs to `Organization`

### Casts

- `BrandProfile` casts:
  - `target_markets_json` to `array`
  - `usp_json` to `array`
  - `objections_json` to `array`
  - `cta_preferences_json` to `array`

## Routes added

- `GET /` redirects to `/dashboard`
- `GET /dashboard` for the authenticated dashboard
- `GET /brand-profile` to edit the brand profile
- `PUT /brand-profile` to update the brand profile

## Seed data added

- `RolePermissionSeeder`
  - creates the `super_admin` role
- `OrganizationSeeder`
  - creates the `Palgoals` organization
  - creates one linked brand profile
- `AdminUserSeeder`
  - creates the default admin user if it does not already exist
  - assigns the `super_admin` role

## Run locally

```bash
php artisan migrate --seed
php artisan serve
```

## Default admin credentials

- Email: `admin@palgoals.com`
- Password: `password`

## Intentionally postponed

- Admin layout polish
- Sidebar navigation
- Settings management UI
- Content engine
- Offers engine
- Marketing planner
- Analyzer
- AI gateway and provider integration
