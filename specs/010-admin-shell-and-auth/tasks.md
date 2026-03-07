# Tasks: Admin Shell and Auth

## Implementation

- [ ] Confirm the auth baseline already present in the repo
- [ ] Add a protected `/admin` route group and a public `/admin/login` boundary
- [ ] Implement the minimal login/logout flow
- [ ] Build a dedicated admin layout shell
- [ ] Mount the first settings editor on top of `SiteSettingsService`
- [ ] Keep admin navigation separate from the public shell

## Documentation

- [ ] Document the auth boundary and operator assumptions
- [ ] Record any first-operator bootstrap step
- [ ] Sync the handoff from `009-admin-site-settings`

## Validation

- [ ] Add feature tests for redirect protection, login, logout, and authenticated admin access
- [ ] Run the relevant validation commands
- [ ] Verify local-first setup still matches the docs
