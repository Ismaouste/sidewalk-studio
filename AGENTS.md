# AGENTS.md

## Repo intent

Sidewalk Studio is a portfolio site and a reusable Laravel reference implementation. Treat public code, docs, and specs as equally important outputs.
GitHub Spec Kit is the official specification standard for this repository.
Codex is the current execution workflow, but this environment must not claim native `/speckit.*` command availability unless it is actually present.

## Working rules

- Keep the repo English-only for code, docs, specs, and public content.
- Prefer local-first workflows: SQLite, `php artisan serve`, `npm run dev`.
- Do not add Docker, deployment files, or CI workflows in this stage unless the user explicitly asks.
- Keep SSR-compatible structure, but do not require the SSR runtime for normal development.
- Follow GitHub Spec Kit phases and artifacts through the repo-local files under `.specify/` and `specs/`.

## Source-of-truth layout

- `.specify/README.md`: repo-local notes on the GitHub Spec Kit alignment
- `.specify/memory/constitution.md`: project constitution
- `.specify/scripts/`: reserved location for GitHub Spec Kit helper scripts if the repo is later bootstrapped through `specify init`
- `.specify/templates/`: reusable templates for specs, plans, tasks, decisions, and release notes
- `specs/<id>/spec.md`: feature intent and acceptance criteria
- `specs/<id>/plan.md`: implementation plan for that feature
- `specs/<id>/tasks.md`: ordered execution checklist
- `docs/`: architecture, AI, consent, SEO, and style references
- `resources/content/`: case studies and writing entries with frontmatter

## Required sync points

When changing the content model, update:

- `docs/architecture/content-system.md`
- `docs/seo/content-model.md`
- `tools/codex/skills/sidewalk-content-editor/` if relevant

When changing consent behavior, update:

- `docs/rgpd/consent-orchestration.md`
- `docs/rgpd/cookies-and-iframes.md`
- `docs/rgpd/analytics-modes.md`

When changing metadata or routing, update:

- `docs/seo/metadata-strategy.md`
- `docs/seo/sitemap-and-robots.md`
- `docs/seo/structured-data.md`

## Validation baseline

- Backend changes: `php artisan test`
- Frontend changes: `npm run types:check` and `npm run build`
- Routing/content changes: `php artisan route:list`

## Repo-local skills

Repo-local skills live in `tools/codex/skills/`.
They are versioned with the project and documented in `docs/ai/skills.md`.
If you want Codex to use them as installed skills, sync them manually into `$CODEX_HOME/skills`.

## Tracking conventions

Keep cross-tool coordination in repo docs before adding automation:

- `docs/ai/spec-kit.md` for spec workflow
- `docs/ai/project-tracking.md` for mapping rules
- `docs/ai/release-planning.md` for release-note and milestone conventions
