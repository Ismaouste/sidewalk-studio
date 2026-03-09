# Sidewalk Studio

Sidewalk Studio is a local-first Laravel portfolio site and a reusable reference for editorial publishing, consent-aware front-end work, SEO, and release-oriented documentation.

This repository now serves two purposes at once:

- a public portfolio surface for Ismael Rodmacq
- a reusable Laravel/Inertia/Vue reference for content, metadata, consent, and static preview export

## What ships in the current release

- Laravel 12 + Inertia.js + Vue 3 + TypeScript application shell
- reusable public design system with token-driven `Morning` and `Sunset` themes
- public pages for `Hello`, `Experience`, `Journal`, `Contact`, `Projects`, `Case Studies`, `Local`, and `Labs`
- Markdown-driven public content in French and English for pages, notes, journal entries, and case studies
- contact form persistence with a lightweight admin inbox
- consent-aware embeds and privacy controls for analytics/media categories
- server-rendered metadata, canonical tags, JSON-LD, `robots.txt`, and `sitemap.xml`
- static preview export for GitHub Pages
- static preview shell with client-side prefetch, installable manifest, and partial offline support

## Product direction

The portfolio is intentionally opinionated:

- local-first development with SQLite and Laravel's built-in server
- public content shaped around real work: e-commerce delivery, product data, CMS work, tracking, consent, connectors, structured data, and editorial systems
- SSR-compatible structure without making SSR runtime mandatory for day-to-day development
- repo-local specs, plans, and release notes kept alongside the codebase

## Stack

- Laravel 12
- PHP 8.4 for CI/static preview
- Inertia.js
- Vue 3
- TypeScript
- Vite
- SQLite by default
- token-based CSS design system
- package-hosted fonts via `@fontsource`
- CookieConsent + IframeManager for consent-aware embeds
- `web-vitals` for front-end metrics instrumentation

## Local development

Use PowerShell on Windows.

```powershell
Copy-Item .env.example .env
New-Item -ItemType File -Force database\database.sqlite | Out-Null
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan db:seed --force
php artisan serve --host=127.0.0.1 --port=8088
npm run dev
```

For a production-like local check:

```powershell
npm run build
Remove-Item public\hot -ErrorAction SilentlyContinue
php artisan serve --host=127.0.0.1 --port=8088
```

## Validation baseline

These commands are the current baseline and were used for the `0.2.0` release:

```powershell
php artisan test
npm run types:check
npm run build
php artisan route:list
php artisan site:export-static-preview --locales=fr,en --output=dist/static-preview --base=/sidewalk-studio/
```

GitHub Actions now mirrors the application-focused checks on pull requests to `main`, on pushes to `main`, and on manual dispatch:

- workflow: `.github/workflows/ci.yml`
- checks: `php artisan test`, `npm run types:check`, `npm run build`, `php artisan route:list`

## Static preview

The repository includes a GitHub Pages preview workflow that exports the public front-end as static HTML with a small app-like shell.

- workflow: `.github/workflows/github-pages-preview.yml`
- exported base path: `/sidewalk-studio/`
- live preview: [https://ismaouste.github.io/sidewalk-studio/](https://ismaouste.github.io/sidewalk-studio/)

The static preview keeps the visual shell, theme switcher, loader, and front-end interactions, while leaving Laravel runtime features such as real form handling and admin behavior out of the public preview.

It now also includes:

- route prefetching for internal links in static preview mode
- a generated `manifest.webmanifest`
- a generated `sw.js` service worker for aggressive asset caching and partial offline navigation
- a portable handoff for another machine: `docs/ai/public-static-handoff.md`

## Portable public data

The public version of the site can now be reconstructed from committed files without relying on a local database.

- localized public identity and contact defaults: `lang/en/site.php`, `lang/fr/site.php`
- public content: `resources/content/`
- fallback site settings snapshot: `database/seeders/data/site-settings.json`
- environment template for file-backed mode: `.env.example`

For the lightweight public setup, keep:

- `SITE_SETTINGS_SOURCE=files`
- `SITE_ENABLE_ADMIN=false`

## Repository map

- `.specify/` stores the repo-local GitHub Spec Kit constitution, templates, and reserved helper-script location
- `specs/` stores feature-level `spec.md`, `plan.md`, and `tasks.md`
- `docs/` stores architecture, consent, SEO, style, AI workflow, career, and release references
- `resources/content/` stores versioned Markdown for pages, writing, and case studies
- `tools/codex/skills/` stores repo-local skills that can be synced into `$CODEX_HOME/skills`

## Release references

- roadmap: `Roadmap.md`
- changelog: `CHANGELOG.md`
- detailed `0.2.0` release note: `docs/ai/release-0.2.0-public-surface.md`

## Near-term follow-up

- locale-path migration from `?lang=` to `/fr/...` and `/en/...`
- richer case studies and public notes based on the new editorial backlog
- footer accessibility controls for motion reduction, theme-transition reduction, and alternate contrast/color modes
- additional front-end performance audits and bundle hygiene
