---
linear_issue: TODO
github_project_item: TODO
github_project_status: validated
obsidian_note: docs/ai/obsidian/build-journal/2026-03-18-014-vercel-preview-runtime.md
release: post-v0
title: Vercel Preview Runtime
status: validated
---

# Feature Specification: Vercel Preview Runtime

A repo-owned Vercel preview runtime for the Laravel application, separate from the existing static GitHub Pages export.

## Problem

The repository could already export a static public preview, but that path intentionally dropped real Laravel runtime behavior. Ad-hoc Vercel staging also exposed two failure modes: raw `index.php` downloads when the project output pointed at `public/`, and broken bootstraps when local Laravel cache manifests referenced development-only service providers.

## Desired outcome

The repo should contain a versioned Vercel preview path that boots Laravel through a stable PHP entrypoint, stores writable runtime state in temp storage, and keeps the public portfolio preview closer to the real app without pretending that full production infrastructure is already in place.

## In scope

- A versioned Vercel entrypoint and project config tracked in the repository
- Preview-safe Laravel bootstrap defaults for cache, sessions, storage, and file-backed site settings
- Documentation for the supported local CLI preview flow
- Tracking updates in roadmap/spec/docs for the new deployment path

## Out of scope

- Full production deployment automation
- Git-based auto-deploy pipelines
- Required SSR runtime in previews
- Durable hosted database infrastructure
- Replacing the existing GitHub Pages static export

## Constraints

- Keep the repo local-first on Windows
- Do not commit secrets or account-specific Vercel metadata
- Keep the public app runnable without requiring an external database for read-only rendering
- Treat Vercel preview as ephemeral runtime infrastructure, not as the production target
- Keep browser assets built locally before deployment

## Acceptance criteria

- `vercel.json` and a repo-owned PHP entrypoint keep `public/` as the static surface and rewrite application requests through a Laravel preview runtime instead of serving raw PHP files.
- The runtime stores writable Laravel state in temp storage and does not rely on stale local bootstrap cache files.
- Preview defaults keep file-backed site settings, cookie sessions, and SSR-disabled rendering unless the environment overrides them.
- The deployment workflow is documented as a local CLI preview path and explicitly separated from the GitHub Pages static export.
- Roadmap, tracking docs, and changelog entries reflect the new preview runtime support.

## Tracking

- Linear: keep the primary issue key in `linear_issue:`
- GitHub Project item: keep the board item key in `github_project_item:`
- GitHub Project status: mirror the board status in `github_project_status:`
- Obsidian note: `docs/ai/obsidian/build-journal/2026-03-18-014-vercel-preview-runtime.md`
