# Sidewalk Studio

Sidewalk Studio is a local-first Laravel portfolio and engineering lab.

The repository is intentionally built as two things at once:

- a public-facing portfolio site
- a reference implementation for privacy-safe, spec-driven web architecture

## Specification standard

The repo uses GitHub Spec Kit as the official specification standard for constitutions, feature specs, plans, and task lists.
Codex remains the current execution workflow in this repo, but the workflow is file-based: do not assume native `/speckit.*` slash commands are available in this Codex environment.

## Current v0 scope

The first release focuses on four concrete areas:

- repository bootstrap and project governance
- Markdown-driven content for writing and case studies
- consent orchestration for scripts and iframe-based media
- SEO foundations with canonical tags, sitemap, robots, and JSON-LD

Out of scope for now:

- Docker-based development
- CI/CD workflows
- deployment automation
- production analytics integrations
- full SSR runtime activation

## Stack

- Laravel 12
- Inertia.js
- Vue 3 + TypeScript
- Tailwind CSS v4
- SQLite for local development
- CookieConsent + IframeManager for consent and embeds

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

If you run the app with `php artisan serve`, update `APP_URL` in `.env` to `http://127.0.0.1:8000`.
If you serve it through Herd or a custom local domain, keep `APP_URL` aligned with that hostname.

## Useful commands

```powershell
php artisan test
composer run ci:check
npm run types:check
npm run build
php artisan route:list
```

## Repository map

- `.specify/` holds the GitHub Spec Kit-aligned constitution, templates, and reserved script location.
- `specs/` holds feature-level `spec.md`, `plan.md`, and `tasks.md` files.
- `docs/` explains architecture, consent, SEO, AI workflow, release planning, and tracking conventions.
- `resources/content/` stores versioned Markdown for writing and case studies.
- `tools/codex/skills/` stores repo-local skills that can be synced into `$CODEX_HOME/skills` if needed.

## Status

The v0 foundation already boots locally and passes `php artisan test`, `composer run ci:check`, and `npm run build`.
The next layer after this foundation is theme/motion polish, richer case study content, CI/CD, and real analytics drivers.
