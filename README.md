# Sidewalk Studio

Sidewalk Studio is a local-first Laravel portfolio site and reference implementation for calm, privacy-aware product engineering.

The repo intentionally serves two roles at once:

- a public site with a coherent editorial and design-system foundation
- a reusable Laravel reference for content, consent, SEO, and release-oriented documentation

## Current repository state

The current branch already includes:

- a Laravel 12 + Inertia + Vue 3 + TypeScript public site foundation
- a token-based `Morning Grid` / `Sunset Signal` theme system
- reusable public design-system primitives and shell components
- public pages for `Home`, `Experience`, `Local`, `Projects`, `Writing`, `Case Studies`, `Contact`, and `Labs`
- repo-versioned Markdown content for Writing and Case Studies under `resources/content/`
- consent orchestration for `necessary`, `analytics`, and `media`
- server-rendered SEO metadata, JSON-LD, `robots.txt`, and `sitemap.xml`
- a bounded `site_settings` read-side foundation for non-secret runtime site values

## Specification and workflow

GitHub Spec Kit is the official specification standard for this repo.
Codex is the current execution workflow, but the workflow remains file-based: do not assume native `/speckit.*` command availability unless it has been verified in the active environment.

## Stack

- Laravel 12
- Inertia.js
- Vue 3 + TypeScript
- Vite
- token-based CSS design system with package-based fonts
- Tailwind CSS v4 in the frontend toolchain
- SQLite for the default local workflow
- CookieConsent + IframeManager for consent-aware embeds

## Local development on Windows

Use PowerShell.

```powershell
Copy-Item .env.example .env
New-Item -ItemType File -Force database\database.sqlite | Out-Null
composer install
npm install
php artisan key:generate
php artisan migrate
npm run dev
```

If you run the app with `php artisan serve`, set `APP_URL` in `.env` to `http://127.0.0.1:8000`.
If you use Herd or another local hostname, keep `APP_URL` aligned with that domain.

## Validation commands

```powershell
php artisan test
composer run ci:check
npm run types:check
npm run build
php artisan route:list
```

## Repository map

- `.specify/` stores the repo-local GitHub Spec Kit constitution, templates, and reserved helper-script location.
- `specs/` stores feature-level `spec.md`, `plan.md`, and `tasks.md` packages.
- `docs/` stores architecture, consent, SEO, style, AI workflow, release, and tracking references.
- `resources/content/` stores versioned Markdown for Writing and Case Studies.
- `tools/codex/skills/` stores repo-local skills that can be synced into `$CODEX_HOME/skills` if needed.

## Tracking and release notes

- `Roadmap.md` is the milestone-order source of truth.
- `CHANGELOG.md` records shipped scope.
- `docs/ai/project-tracking.md` and related files under `docs/ai/linear/` and `docs/ai/github-project/` prepare manual backfill for real project tracking.

## Current limits and deferred work

Still deferred:

- Docker-based development
- CI/CD and deployment automation
- production analytics integrations
- full SSR runtime activation
- a protected write UI for `site_settings`
- any live CMS migration for the public site
