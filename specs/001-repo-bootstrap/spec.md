---
linear_issue: SID-5
github_project_item: TODO
github_project_status: validated
obsidian_note: docs/ai/obsidian/build-journal/2026-03-07-001-repo-bootstrap.md
release: v0-foundation
title: Repository Bootstrap
status: active
---

# Feature Specification: Repository Bootstrap

A GitHub Spec Kit-aligned repository foundation for a Laravel portfolio and reference implementation.

## Problem

The project needs a stable structure for application code, specs, docs, and workflow rules before feature work can scale.

## Desired outcome

The repo should boot locally on Windows, keep its docs/specs visible, and follow GitHub Spec Kit artifact conventions without pretending Codex has native `/speckit.*` commands.

## In scope

- Laravel application bootstrap and local SQLite workflow
- Repo normalization around `.specify/`, `specs/`, and `docs/`
- AI-operational docs, governance files, and repo-local skills

## Out of scope

- Docker-based development
- CI/CD and deployment automation

## Constraints

- Windows local-first workflow
- English-only repo docs and public content

## Acceptance criteria

- Laravel app boots locally with SQLite.
- README reflects the real setup.
- `AGENTS.md`, `docs/ai`, and repo-local skills exist.
- `.github` templates are in the correct path and naming shape.

## Tracking

- Linear: keep the primary key in `linear_issue:`
- GitHub Project item: keep the board item key in `github_project_item:`
- GitHub Project status: mirror the board status in `github_project_status:`
- Obsidian note: `docs/ai/obsidian/build-journal/2026-03-07-001-repo-bootstrap.md`
