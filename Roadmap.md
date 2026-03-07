# Roadmap

## Active foundation

### 001-repo-bootstrap
- Normalize the repo around Laravel, `.specify/`, `specs/`, and the docs tree.
- Keep development local-first on Windows with SQLite.
- Keep SQLite as the v0 default and avoid planning any server database migration before a concrete product need exists.
- Align the repo with GitHub Spec Kit as the specification standard while keeping Codex as the current file-based execution workflow.
- Add AI-operational docs, repo-local skills, and governance templates.

### 002-content-system
- Keep writing and case studies in Markdown under `resources/content/`.
- Enforce explicit frontmatter for slugs, state, timestamps, tags, and SEO fields.
- Expose public index/detail pages through Inertia routes.

### 003-consent-orchestration
- Maintain the `necessary`, `analytics`, and `media` categories only.
- Keep analytics as a no-op driver until the dedicated analytics spec lands.
- Gate iframe embeds behind explicit media consent.

### 004-seo-foundation
- Keep canonical URLs, JSON-LD, sitemap, and robots generated from the app itself.
- Serve metadata server-side for the first HTML even before SSR is enabled.

## Deferred after v0 foundation

### Database strategy
- Keep SQLite for v0 and the default local workflow.
- Prefer PostgreSQL as the first database to explore when the product actually needs a server database.
- Defer any server database migration until that need is explicit.

### 005-theme-and-motion
- Expand the visual system, motion primitives, and theme toggle.

### 006-case-studies
- Add richer editorial content and longer walkthroughs.

### 007-ci-cd-foundation
- Add GitHub Actions, release checks, and automated validation.

### 008-analytics-modes
- Add real Matomo and/or PostHog adapters on top of the existing consent registry.
