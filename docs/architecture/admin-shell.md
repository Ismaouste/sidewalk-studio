# Admin Shell

The admin shell is a minimal operator boundary for Sidewalk Studio.

## Scope

- production-safe first-run onboarding
- session-based authentication on the existing Laravel `users` table
- protected `/admin` routes
- a dedicated Inertia admin layout separate from the public shell
- bounded editors for site settings, theme/publishing, publications, pages, and managed language files
- a read-only audit log for recent sensitive operator actions

## Route boundary

- `/admin` is now the first-class product entry point
- `/admin/onboarding` is available only while no operator exists yet
- `/admin/login` is the normal auth entry point once onboarding is complete
- `/admin/settings`, `/admin/theme`, `/admin/publications`, `/admin/pages`, `/admin/language-files`, and `/admin/logout` are protected by `App\Http\Middleware\AdminAuthenticate`

## Operator bootstrap

There is no public registration flow.

The preferred production bootstrap is the first-run onboarding flow under `/admin/onboarding`.

For local convenience, `php artisan db:seed` can still seed the project
settings plus one operator when these env vars are present:

```dotenv
ADMIN_SEED_EMAIL=admin@sidewalk-studio.test
ADMIN_SEED_NAME="Sidewalk Admin"
ADMIN_SEED_PASSWORD="change-me-local"
```

That remains a local/development convenience path when recreating the project on another machine or on a temporary host.

The canonical `site_settings` row is now versioned through
`database/seeders/data/site-settings.json`. If you update those values through
the admin UI and want to bring them back into the repo, export them with:

```powershell
php artisan site:export-settings
```

Create or rotate an operator manually with:

```powershell
php artisan admin:create-user you@example.test --name="Operator Name" --password="change-me"
```

If `--name` or `--password` is omitted, the command prompts interactively.

## Settings integration

- the admin settings page reads from `SiteSettingsService::current()`
- updates go through `SiteSettingsService::update()`
- validation still lives in the existing `SiteSettings` payload contract
- successful writes refresh the cache, create an audit entry, and redirect back with a flash status message

## Content and publishing integration

- publications use a hybrid read layer backed by `publications` plus repo-owned Markdown fallback
- static pages use a hybrid read layer backed by `pages` plus repo-owned page frontmatter fallback
- managed language/site copy writes back to `lang/en/site.php` and `lang/fr/site.php`
- theme, static export controls, and rebuild state live in the extended `site_settings` singleton
- the rebuild action is synchronous in the first version and can trigger the static preview export command directly

## Audit trail

- `/admin/audit-log` is a protected read-only view over recent admin audit entries
- the first milestone records successful `site_settings` updates and successful `admin:create-user` operator bootstrap actions
- audit entries persist actor, action, subject, a compact summary, and the write timestamp
- raw secrets, raw settings payload values, and operator passwords are intentionally excluded from the audit summary
