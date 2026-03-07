# GitHub Spec Kit Workflow

This repo uses GitHub Spec Kit as the official specification standard and keeps a small, explicit spec kit on disk so feature work stays reviewable and reusable.

## Directory roles

- `.specify/README.md` explains how this repo aligns with GitHub Spec Kit while staying practical for Codex.
- `.specify/memory/constitution.md` defines the non-negotiable project principles.
- `.specify/templates/` stores reusable templates for specs, plans, tasks, decisions, build logs, and release notes.
- `.specify/scripts/` is the reserved location for future GitHub Spec Kit helper scripts.
- `specs/<id>/` stores the approved spec package for one feature.
- `docs/ai/` stores tracking conventions and cross-tool coordination notes.

## Alignment rules

- Use GitHub Spec Kit phase names: constitution, specify, plan, tasks, implement.
- Keep the actual source of truth in repo files, not transient prompts.
- Preserve the existing top-level `specs/` directory for feature packages in this repo.
- Treat GitHub Spec Kit as the standard for artifact shape, not as a claim that every generated command is available in Codex today.

## Standard flow

1. Start from the constitution and `Roadmap.md`.
2. Create or update `specs/<id>/spec.md` from `.specify/templates/spec-template.md`.
3. Add `plan.md` and `tasks.md` from the matching templates.
4. Mirror tracking status in the files under `docs/ai/`.
5. Implement the smallest useful slice.
6. Update docs, validation notes, and release notes when the shipped scope moves.

## Current Codex execution

- Codex is the current executor for this repo.
- Use `AGENTS.md`, repo-local skills, and the file-based artifacts in `.specify/` and `specs/`.
- Do not tell contributors that `/speckit.constitution`, `/speckit.specify`, `/speckit.plan`, `/speckit.tasks`, or `/speckit.implement` are natively available in Codex unless that has been verified in the active environment.

## Minimal tracking contract

- Specs are the source of truth for intent and acceptance criteria.
- `linear_issue:` in `spec.md` is the primary issue key when Linear exists.
- GitHub Project and Obsidian stay mirrored through the docs under `docs/ai/`.
- Release summaries belong in `CHANGELOG.md` and any release note derived from `.specify/templates/release-note-template.md`.
