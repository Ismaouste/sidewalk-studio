# Tasks: Admin Shell and Auth

## Implementation

- [x] Confirm the auth baseline already present in the repo
- [x] Add a protected `/admin` route group and a public `/admin/login` boundary
- [x] Implement the minimal login/logout flow
- [x] Build a dedicated admin layout shell
- [x] Mount the first settings editor on top of `SiteSettingsService`
- [x] Keep admin navigation separate from the public shell

## Documentation

- [x] Document the auth boundary and operator assumptions
- [x] Record any first-operator bootstrap step
- [x] Sync the handoff from `009-admin-site-settings`

## Validation

- [x] Add feature tests for redirect protection, login, logout, and authenticated admin access
- [x] Run the relevant validation commands
- [x] Verify local-first setup still matches the docs
