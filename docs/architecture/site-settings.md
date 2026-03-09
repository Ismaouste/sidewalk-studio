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
- `site_settings` is not a future Sanity responsibility; remote editorial tooling must not become the source of truth for runtime configuration.
- API keys and other secrets do not belong in `site_settings`; if admin-managed secrets are needed later, they require a separate encrypted store with tighter access rules.
- Editorial content can now live in the database, but repo-owned Markdown remains the import/fallback layer for portable public mode.

## First settings groups

- `site_identity`
- `contact_details`
- `social_links`
- `seo_defaults`
- `consent_copy`
- `feature_toggles`
- `theme_settings`
- `static_export_settings`
- `publishing_state`
- `admin_state`

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
- `App\Services\SiteSettingsService::update()` is now the narrow validated write contract for the singleton aggregate.
- The first protected admin editor now mounts on top of that service under `/admin/settings`.
- successful admin writes now add a compact audit entry under `/admin/audit-log`
- audit summaries record changed groups and field names only; they do not store raw settings values or secrets
- onboarding completion now also writes through this singleton so the product can tell whether a first operator has already been created
- rebuild status now lives in `publishing_state` so the admin UI can surface whether public output needs regeneration

## Database strategy

- SQLite remains the initial persistence layer because it matches the current local-first workflow.
- PostgreSQL is the preferred future migration path if real product needs justify a server database.
- No server database migration is required to start this feature.
