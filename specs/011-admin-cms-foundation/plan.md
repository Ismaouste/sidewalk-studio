# Implementation Plan: Admin CMS foundation and production onboarding

## Summary

The implementation extends the existing `site_settings` singleton for onboarding, theme, export, and publishing state, adds hybrid database/file-backed repositories for publications and pages, and expands the Inertia admin shell with content, copy, and rebuild flows.

## Decisions

- Keep `SiteSettingsService` as the runtime singleton and extend it rather than introducing a second settings aggregate
- Use hybrid read repositories so production can prefer database edits without losing file-backed portability
- Keep language/site copy file-backed, but edit it through a structured admin UI with deterministic PHP array writes

## Main changes

- Add onboarding-aware `/admin` entry and first-run operator flow
- Add `publications`, `pages`, and `publication_type_settings` persistence
- Extend the admin shell with publications, pages, language files, and theme/publishing screens
- Add synchronous rebuild behavior that clears caches and optionally regenerates the static preview

## Docs and tracking sync

- Specs updated: `spec.md`, `plan.md`, `tasks.md`
- Relevant docs updated if architecture, content, consent, or SEO changed
- Tracking maps updated in `docs/ai/` if the feature status moved

## Validation

- `php artisan test`
- `composer run ci:check`
- `npm run build`
- `npm run build:ssr` when SSR compatibility changed
