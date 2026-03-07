# Implementation Plan: Admin Site Settings

## Summary

Introduce a small protected settings module that centralizes a bounded set of site-wide values behind typed validation and one application service, while keeping the existing content and configuration model stable.

## Decisions

- Keep the first implementation SQLite-friendly and migration-friendly for a later PostgreSQL path
- Limit the first slice to global site settings, not editorial content management

## Main changes

- Add a protected admin route group and UI shell for site settings
- Add typed persistence, validation, and a single read service for public-page consumers
- Define a safe migration path from env/config defaults to stored settings where appropriate

## Docs and tracking sync

- Update architecture docs when the settings source of truth changes
- Update SEO or consent docs if metadata defaults or consent copy move into the settings module
- Mirror tracking changes in `docs/ai/`

## Validation

- `php artisan test`
- `composer run ci:check`
- `npm run build`
