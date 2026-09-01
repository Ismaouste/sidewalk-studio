# Sidewalk Studio

The open-source site of a working freelance practice: web and e-commerce
engineering with published prices, part-time technical direction, and a
consent-first approach to marketing — built as a living reference you can
read, run, and fork.

The live site is the portfolio of [Ismaël Rodmacq](https://github.com/Ismaouste)
(tech lead / e-commerce engineer, Nancy, France). This repository is the case
study behind it: every feature the site sells is demonstrated by the site
itself.

## What this project demonstrates

- **A declared content schema** — pages are typed declarations
  (`app/Content/Schema/`), stored as FR/EN Markdown in strict shape parity,
  editable from a generated back office, with the database and the files as
  interchangeable sources.
- **Consent-first marketing machinery** — a three-tier measurement design
  (`config/consent.php`, `docs/architecture/measurement.md`, `docs/rgpd/`):
  a first-party CNIL-exemptable audience ping, PostHog EU behind explicit
  opt-in, session replay behind its own switch — never part of "Accept all".
- **Platform primitives over components** — Popover API, `<details>`,
  `@starting-style`, scroll-driven animations, View Transitions, Speculation
  Rules, `@layer`, `content-visibility`; smart CSS over lifecycle hooks.
- **A hand-authored design system** — no CSS framework; two themes
  (`morning`, architectural daylight; `sunset`, violet glass) retunable from
  a six-hex accent block in `resources/css/tokens.css`.
- **Bilingual publishing with guarantees** — FR/EN parity enforced by tests
  at the copy, lang-file, and content-frontmatter layers.
- **SEO as engineering** — hand-rolled sitemap, canonical strategy, JSON-LD
  variants, per-article generated SVG visuals, Core Web Vitals budget.

## Stack

Laravel 13 · Inertia 3 · Vue 3 · TypeScript 5.9 · Vite 8 (Rolldown) ·
SQLite locally, no database required in production · Vercel runtime +
GitHub Pages static export.

## Quickstart

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

## Validation baseline

```powershell
npm run check          # lint + format + types
php artisan test
npm run build
php artisan route:list
```

CI mirrors these on every push and pull request (`.github/workflows/ci.yml`).

## Forking it

The machine is yours under the MIT license; the words are not. Identity
lives in exactly two places — `lang/{locale}/site.php` and
`database/seeders/data/site-settings.json` — and `SiteIsAgnosticTest` fails
if a name leaks anywhere else. Replace those two files and
`resources/content/`, and the site is yours. See [LICENSE](LICENSE) for the
scope note and [CONTRIBUTING.md](CONTRIBUTING.md) for what is open.

## Previews

- **Static preview** (GitHub Pages, public shell only):
  [ismaouste.github.io/sidewalk-studio](https://ismaouste.github.io/sidewalk-studio/)
  — exported by `php artisan site:export-static-preview`, with prefetch,
  manifest, and partial offline support.
- **Vercel runtime preview** — the real Laravel app via `npx vercel deploy`
  from a prepared workspace; see
  `docs/architecture/vercel-preview-runtime.md`.

## Repository map

- `.specify/` + `specs/` — GitHub Spec Kit constitution and feature specs
- `docs/` — architecture, style, RGPD, SEO, AI-workflow, and release notes
- `docs/superpowers/` — design docs and implementation plans, committed as
  they are used
- `resources/content/` — versioned FR/EN Markdown (all rights reserved)
- `app/Content/Schema/` — the declared content model

## License

Code under [MIT](LICENSE). Content (`resources/content/`, `docs/career/`),
name, and brand assets: all rights reserved — the scope note in the LICENSE
file draws the exact line.
