# Implementation Plan: Admin Site Settings

## Summary

Introduce a bounded `site_settings` domain that centralizes non-secret site-wide runtime values behind typed validation, one application service, and a cacheable read path. Keep the existing env/config and Markdown model stable while creating the domain that `010-admin-shell-and-auth` can later expose through a protected UI.

## Decisions

- Keep the first implementation SQLite-friendly and migration-friendly for a later PostgreSQL path
- Keep `.env` for secrets and infrastructure; use `site_settings` only for bounded non-secret runtime config
- Treat admin-managed secrets as a separate future concern, not as part of `site_settings`
- Model the first version as one bounded aggregate, not an arbitrary settings bag
- Keep the operator shell/auth boundary in `010-admin-shell-and-auth`

## Main changes

- Add the `site_settings` persistence layer with explicit groups and typed access
- Add the settings service, cache behavior, and fallback/default bootstrapping path
- Refactor public consumers to read site-wide values through one service
- Define the write contract that a later protected admin shell can call
- Keep migration and seeding paths SQLite-first and PostgreSQL-friendly

## Docs and tracking sync

- Add `docs/architecture/site-settings.md`
- Add a durable decision note for the `.env` versus `site_settings` boundary
- Update architecture docs when the settings source of truth changes
- Update SEO or consent docs if metadata defaults or consent copy move into the settings module
- Mirror tracking changes in `docs/ai/`

## Validation

- add tests for defaults, validation, cache behavior, and shared public consumption
- `php artisan test`
- `composer run ci:check`
- `npm run build`
