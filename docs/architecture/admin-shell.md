# Admin Shell

The admin shell is a minimal operator boundary for Sidewalk Studio.

## Scope

- session-based authentication on the existing Laravel `users` table
- protected `/admin` routes
- a dedicated Inertia admin layout separate from the public shell
- the first bounded editor for `site_settings`

## Route boundary

- `/admin/login` is the public auth entry point
- `/admin`, `/admin/settings`, and `/admin/logout` are protected by `App\Http\Middleware\AdminAuthenticate`
- authenticated requests land on `/admin/settings`

## Operator bootstrap

There is no public registration flow.

Create the first local operator with:

```powershell
php artisan admin:create-user you@example.test --name="Operator Name" --password="change-me"
```

If `--name` or `--password` is omitted, the command prompts interactively.

## Settings integration

- the admin settings page reads from `SiteSettingsService::current()`
- updates go through `SiteSettingsService::update()`
- validation still lives in the existing `SiteSettings` payload contract
- successful writes refresh the cache and redirect back with a flash status message
