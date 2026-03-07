# Project Tracking

This repo keeps project tracking simple in v0.
The goal is to make future Linear, Obsidian, and GitHub Project connections predictable without adding fragile API glue before the local workflow is stable.
GitHub Spec Kit is the specification standard; Codex is the current execution workflow.

## Canonical rules

- Specs remain the source of truth for feature intent and acceptance criteria.
- `Roadmap.md` remains the source of truth for milestone ordering.
- `specs/<id>/spec.md` stores the optional `linear_issue:` field for one-to-one feature mapping.
- Repo docs store stable coordination notes; local tools can sync or mirror them later.

## File map

- `docs/ai/linear/spec-issue-map.md` mirrors spec and Linear relationships.
- `docs/ai/github-project/roadmap-spec-issue-map.md` mirrors roadmap/spec/release status.
- `docs/ai/obsidian/build-journal/` stores repo-safe note mirrors.
- `.specify/templates/` stores the templates that feed those files.

## Linear

Use Linear as the execution tracker once tickets exist, but keep the feature contract in the repo.

- Store the Linear issue key in `specs/<id>/spec.md` frontmatter under `linear_issue:`.
- Keep the cross-spec summary in `docs/ai/linear/spec-issue-map.md`.
- If one spec expands into multiple Linear issues later, keep the primary key in `spec.md` and list the breakdown in the map file.

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

## Suggested next step

When the first external tooling pass starts, wire the simplest path first:

1. Fill `linear_issue:` in the existing spec frontmatter.
2. Mirror the same keys in the map files under `docs/ai/`.
3. Only then evaluate lightweight export/import automation if the manual flow becomes noisy.

## Naming conventions

- Spec IDs stay numeric and ordered: `001-...`, `002-...`
- Obsidian mirror files should start with `YYYY-MM-DD-`
- Release notes should use the shipped milestone name, for example `v0-foundation`
