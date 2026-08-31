# Roadmap, Specs, and GitHub Project Map

Use this file as the repo-side mirror for a future GitHub Project board.
It keeps the roadmap order, spec folders, and future issue trackers aligned without requiring API integration in v0.

| Roadmap block | Spec folder | Linear issue | GitHub Project item | GitHub Project status | Obsidian note | Release | Notes |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `001-repo-bootstrap` | `specs/001-repo-bootstrap/` | `SID-5` | `TODO` | `validated` | `docs/ai/obsidian/build-journal/2026-03-07-001-repo-bootstrap.md` | `v0-foundation` | Repo structure, governance, and local-first baseline are in place. |
| `002-content-system` | `specs/002-content-system/` | `SID-6` | `TODO` | `validated` | `docs/ai/obsidian/build-journal/2026-03-07-002-content-system.md` | `v0-foundation` | Markdown content system and public routes are in place. |
| `003-consent-orchestration` | `specs/003-consent-orchestration/` | `SID-7` | `TODO` | `validated` | `docs/ai/obsidian/build-journal/2026-03-07-003-consent-orchestration.md` | `v0-foundation` | Consent categories and media gating are in place. |
| `004-seo-foundation` | `specs/004-seo-foundation/` | `SID-8` | `TODO` | `validated` | `docs/ai/obsidian/build-journal/2026-03-07-004-seo-foundation.md` | `v0-foundation` | Metadata, sitemap, robots, and JSON-LD are in place. |
| `014-vercel-preview-runtime` | `specs/014-vercel-preview-runtime/` | `TODO` | `TODO` | `validated` | `docs/ai/obsidian/build-journal/2026-03-18-014-vercel-preview-runtime.md` | `post-v0` | Repo-owned Vercel preview runtime is in place for local CLI previews of the Laravel app. |
| `016-declared-content-schema` | `specs/016-declared-content-schema/` | `TODO` | `TODO` | `implemented` | `TODO` | `post-v0` | Content model declared in `app/Content/Schema/`; database authoritative with Markdown as the seed; page editor generated from the declaration. |
| `005-theme-and-motion` | `TBD` | `TBD` | `TBD` | `deferred` | `TBD` | `post-v0` | Next visual-system expansion after the foundation release. |
| `006-case-studies` | `TBD` | `TBD` | `TBD` | `deferred` | `TBD` | `post-v0` | Richer editorial content and walkthrough depth. |
| `007-ci-cd-foundation` | `TBD` | `TBD` | `TBD` | `deferred` | `TBD` | `post-v0` | Validation automation after the local workflow is settled. |
| `008-analytics-modes` | `TBD` | `TBD` | `TBD` | `deferred` | `TBD` | `post-v0` | Real analytics adapters on top of the existing consent registry. |
| `009-admin-site-settings` | `specs/009-admin-site-settings/` | `TODO` | `TODO` | `proposed` | `docs/ai/obsidian/build-journal/2026-03-07-009-admin-site-settings.md` | `post-v0` | Read-side settings service is in place; protected write surface remains deferred. |
| `010-admin-shell-and-auth` | `TBD` | `TBD` | `TBD` | `proposed` | `TBD` | `post-v0` | Planned admin boundary for protected operational features. |
| `011-admin-audit-log` | `TBD` | `TBD` | `TBD` | `proposed` | `TBD` | `post-v0` | Planned audit trail for settings and later admin actions. |
| `012-installation-onboarding` | `TBD` | `TBD` | `TBD` | `proposed` | `TBD` | `post-v0` | Planned first-run onboarding and initial operator bootstrap. |
