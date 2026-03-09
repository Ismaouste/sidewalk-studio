---
linear_issue: TODO
github_project_item: TODO
github_project_status: proposed
obsidian_note: TODO
release: TODO
title: Admin CMS foundation and production onboarding
status: proposed
---

# Feature Specification: Admin CMS foundation and production onboarding

This feature turns the existing bounded admin shell into a production-ready editorial and configuration surface for Sidewalk Studio.

## Problem

Today the admin shell depends on a local-only feature toggle, the public content model is mostly file-backed, and editors still need manual file edits for major content and copy changes.

## Desired outcome

Sidewalk Studio should support a first-run onboarding flow, database-backed content management for publications, hybrid page management, managed language-file editing, and an explicit rebuild/export workflow while preserving the public site and static export path.

## In scope

- Production-safe admin onboarding
- Database-backed publications with file import/fallback
- Hybrid page management
- Managed language-file editing
- Theme/static export/rebuild admin surface
- Docs and spec updates for the new normalized content flow

## Out of scope

- Queue-based background publishing
- Third-party CMS integration
- Deployment infrastructure automation

## Constraints

- Keep public routes and current static export behavior working
- Keep code, docs, specs, and admin UX English-only
- Preserve SQLite-friendly local workflows

## Acceptance criteria

- [ ] `/admin` leads to onboarding when no operator exists, and to normal auth afterward
- [ ] Publications can be managed for `note`, `journal`, and `case_study`
- [ ] Public pages appear in admin and expose editable SEO/runtime payload fields
- [ ] Managed language files can be edited through a structured UI
- [ ] Theme, static export controls, and rebuild state are visible in admin
- [ ] The public site and static export continue to use a normalized read layer

## Tracking

- Linear: keep the primary issue key in `linear_issue:`
- GitHub Project: mirror `github_project_item`, `github_project_status`, and `release` in `docs/ai/github-project/roadmap-spec-issue-map.md`
- Obsidian: set `obsidian_note` to the repo mirror path under `docs/ai/obsidian/build-journal/`
- Codex execution: use the file-based workflow even if native `/speckit.*` commands are unavailable
