# Admin Shell

The admin shell is a minimal operator boundary for Sidewalk Studio.

## Scope

- session-based authentication on the existing Laravel `users` table
- protected `/admin` routes
- a dedicated Inertia admin layout separate from the public shell
- the first bounded editor for `site_settings`
- a read-only audit log for recent sensitive operator actions

## Route boundary

- `/admin/login` is the public auth entry point
- `/admin`, `/admin/settings`, and `/admin/logout` are protected by `App\Http\Middleware\AdminAuthenticate`
- authenticated requests land on `/admin/settings`

## Operator bootstrap

There is no public registration flow.

For a portable local bootstrap, `php artisan db:seed` now seeds the project
settings plus one operator when these env vars are present:

```dotenv
ADMIN_SEED_EMAIL=admin@sidewalk-studio.test
ADMIN_SEED_NAME="Sidewalk Admin"
ADMIN_SEED_PASSWORD="change-me-local"
```

That is the recommended path when recreating the project on another machine or
on a temporary host.

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

## Audit trail

- `/admin/audit-log` is a protected read-only view over recent admin audit entries
- the first milestone records successful `site_settings` updates and successful `admin:create-user` operator bootstrap actions
- audit entries persist actor, action, subject, a compact summary, and the write timestamp
- raw secrets, raw settings payload values, and operator passwords are intentionally excluded from the audit summary
