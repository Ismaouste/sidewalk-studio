# Project Tracking

This repo keeps project tracking simple in v0.
The goal is to make future Linear, Obsidian, and GitHub Project connections predictable without adding fragile API glue before the local workflow is stable.
GitHub Spec Kit is the specification standard; Codex is the current execution workflow.

## Canonical rules

- Specs remain the source of truth for feature intent and acceptance criteria.
- `Roadmap.md` remains the source of truth for milestone ordering.
- `specs/<id>/spec.md` stores the tracking fields that mirror the current delivery state.
- Repo docs store stable coordination notes; local tools can sync or mirror them later.

## Spec frontmatter tracking contract

Keep these fields in each `spec.md` once a feature enters active tracking:

- `linear_issue:` primary Linear issue key, for example `SIDE-12`
- `github_project_item:` GitHub Project item identifier or stable title reference
- `github_project_status:` mirror of the board status, using the repo vocabulary
- `obsidian_note:` path to the repo-safe build journal mirror
- `release:` shipped or target milestone, for example `v0-foundation`

## File map

- `docs/ai/linear/spec-issue-map.md` mirrors spec and Linear relationships.
- `docs/ai/linear/issue-bootstrap.md` gives the first manual Linear setup for specs `001` to `004`.
- `docs/ai/github-project/roadmap-spec-issue-map.md` mirrors roadmap/spec/release status.
- `docs/ai/github-project/field-schema.md` defines the initial GitHub Project fields.
- `docs/ai/obsidian/build-journal/` stores repo-safe note mirrors.
- `.specify/templates/` stores the templates that feed those files.

## Linear

Use Linear as the execution tracker once tickets exist, but keep the feature contract in the repo.

- Store the Linear issue key in `specs/<id>/spec.md` frontmatter under `linear_issue:`.
- Keep the cross-spec summary in `docs/ai/linear/spec-issue-map.md`.
- If one spec expands into multiple Linear issues later, keep the primary key in `spec.md` and list the breakdown in the map file.
- Use `docs/ai/linear/issue-bootstrap.md` as the initial working setup for specs `001` to `004`.

## Obsidian

Use Obsidian for fast private thinking, build notes, and rough decision logs.

- Keep raw build journal notes in the Obsidian vault first.
- Mirror stable repo-facing summaries in `docs/ai/obsidian/build-journal/`.
- Promote durable architecture decisions from notes into `docs/architecture/decisions/`.
- Do not treat the vault as the public source of truth for behavior that affects the app.

## GitHub Project

Use GitHub Project as the release and delivery board, not as the spec source.

- Track roadmap blocks and feature progress in `docs/ai/github-project/roadmap-spec-issue-map.md`.
- Use one row per roadmap/spec item so the mapping stays readable during release prep.
- Keep status labels aligned with the repo language: `proposed`, `active`, `validated`, `deferred`.
- Use `docs/ai/github-project/field-schema.md` as the initial field contract for the first board.

## Suggested next step

When the first external tooling pass starts, wire the simplest path first:

1. Fill `linear_issue:` in the existing spec frontmatter.
2. Fill `github_project_item:`, `github_project_status:`, and `release:` in the same spec.
3. Create or update the matching Obsidian mirror note and store its repo path in `obsidian_note:`.
4. Mirror the same values in the map files under `docs/ai/`.
5. Only then evaluate lightweight export/import automation if the manual flow becomes noisy.

## Current bootstrap

Specs `001` to `004` now have:

- tracking-ready frontmatter fields in each `spec.md`
- placeholder rows in the Linear and GitHub Project map files
- dedicated repo-safe Obsidian mirror notes under `docs/ai/obsidian/build-journal/`

## Naming conventions

- Spec IDs stay numeric and ordered: `001-...`, `002-...`
- Obsidian mirror files should start with `YYYY-MM-DD-`
- Release notes should use the shipped milestone name, for example `v0-foundation`
