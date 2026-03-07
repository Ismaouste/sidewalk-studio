# Site Settings

`site_settings` exists to hold bounded non-secret runtime configuration that may change without a deploy.

## Why this exists

The repo already has three different configuration surfaces:

- `.env` for secrets and runtime infrastructure
- `config/*.php` for application defaults and bootstrap behavior
- Markdown content for writing and case studies

That split is still correct, but global site values such as public contact details, SEO defaults, consent copy, and non-sensitive toggles need a clearer home once updates become more frequent.

## Boundary

- `.env` remains the source of truth for secrets, credentials, provider keys, mail config, DB connection, app key, and similar runtime-sensitive values.
- `site_settings` is for bounded non-secret runtime configuration.
- API keys and other secrets do not belong in `site_settings`; if admin-managed secrets are needed later, they require a separate encrypted store with tighter access rules.
- Markdown remains the source of truth for editorial content.

## First settings groups

- `site_identity`
- `contact_details`
- `social_links`
- `seo_defaults`
- `consent_copy`
- `feature_toggles`

## Defaults and bootstrapping

- The first persisted payload should bootstrap from the current config and env-backed defaults.
- The bootstrap must be idempotent so it can seed an empty install without overwriting later edits.
- Public reads should still have safe defaults if no persisted row exists yet.
- The current runtime path uses `App\Services\SiteSettingsService` for reads and `Database\Seeders\SiteSettingsSeeder` as the first explicit bootstrap helper.

## Consumption and cache

- Public pages should read site-wide values through one application service.
- That service should cache the hydrated settings payload and invalidate or refresh it after writes.
- Controllers and support classes should not duplicate fallback logic.
- The first read-side integration now covers shared Inertia props, home/contact page reads, and default SEO metadata inputs.

## Database strategy

- SQLite remains the initial persistence layer because it matches the current local-first workflow.
- PostgreSQL is the preferred future migration path if real product needs justify a server database.
- No server database migration is required to start this feature.
