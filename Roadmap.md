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

### 009-admin-site-settings
- Add the bounded `site_settings` domain for site metadata, contact details, consent copy, and future runtime toggles.
- Keep SQLite as the first persistence layer and stay migration-friendly for a later PostgreSQL path if product needs justify it.
- Do not replace `.env`, store API keys in `site_settings`, replace Markdown content, introduce a full CMS, or collapse the future admin shell/auth work into this phase.

### 010-admin-shell-and-auth
- Add the protected admin route group, operator authentication, and the internal shell that future operational features can reuse.
- Mount `009-admin-site-settings` behind this boundary instead of coupling settings persistence to ad-hoc route protection.

### 011-admin-audit-log
- Add an audit trail for settings writes and later admin actions.
- Keep the first scope narrow and readable: actor, action, subject, payload summary, and timestamp.

### 012-installation-onboarding
- Add first-run installation and onboarding for creating the initial operator account and seeding the first `site_settings` row.
- Keep the bootstrap path compatible with local SQLite and later server onboarding without requiring PostgreSQL first.

### 013-sanity-content-evaluation
- Revisit Sanity only as a bounded editorial content layer, not as a replacement for core public pages or `site_settings`.
- Keep `Home`, `Experience`, `Local`, and `Contact` code-driven.
- Keep Writing and Case Studies repo-backed until a concrete editorial workflow justifies a pilot.
- If explored later, prefer a new low-risk collection such as `local_notes` before touching existing Markdown domains.
