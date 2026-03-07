# Plan: Admin Shell and Auth

## Goal

Introduce a minimal protected admin boundary that can safely host the existing site settings write path.

## Sequence

1. Confirm the existing Laravel auth baseline in the repo and choose the smallest session-based implementation that fits it.
2. Add protected admin routes and middleware boundaries without altering the public shell.
3. Build a dedicated admin layout and login screen through Inertia pages.
4. Mount the first settings page on top of `SiteSettingsService`.
5. Add feature tests for login, redirect protection, logout, and settings-page access.

## Notes

- Keep the first operator model intentionally small.
- Prefer native Laravel auth primitives over custom auth abstractions.
- If operator account bootstrap is still missing, add a local-first setup step rather than public registration.
