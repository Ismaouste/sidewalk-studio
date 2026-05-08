---
name: spec-kit-bootstrap
description: Bootstraps a new feature spec following GitHub Spec Kit conventions used in this repo. Use when starting a new feature, scoping a new chunk of work, or when the user invokes /spec-kit-bootstrap. Creates specs/<NNN>-<slug>/{spec,plan,tasks}.md from .specify/templates/.
disable-model-invocation: true
---

# Spec Kit Bootstrap

Use this skill when the user wants to start a new feature spec. It scaffolds the three Spec Kit artifacts from the templates already in `.specify/templates/`.

## Inputs

- A short feature name from the user (will be slugified).
- Optional one-line summary.

## Steps

1. List `specs/` to find the highest existing 3-digit prefix; the new ID is `prefix + 1`, zero-padded to 3 digits.
2. Slugify the feature name: lowercase, ASCII, hyphen-separated, strip diacritics.
3. Read all three templates:
   - `.specify/templates/spec-template.md`
   - `.specify/templates/plan-template.md`
   - `.specify/templates/tasks-template.md`
4. Create `specs/<NNN>-<slug>/` and write the three files, replacing template placeholders with the user's input. Leave any block that requires user judgment as a `TODO` with a clear hint.
5. Print to the user:
   - The three created paths
   - Which placeholders still need filling
   - A reminder to fill `spec.md` first, then run `superpowers:writing-plans` to expand `plan.md`

## Conventions

- IDs are zero-padded 3-digit (e.g., `010`, `011`).
- Slugs are lowercase, hyphen-separated, no diacritics, no leading/trailing dashes.
- Do not run validation, tests, or builds — these are scaffolds.
- Do not commit; let the user review the scaffold first.

## When NOT to use

- Updating an existing spec — edit the files directly.
- Tracking work that does not warrant a spec (one-off bug fix, doc tweak) — use a TodoWrite/TaskCreate instead.
