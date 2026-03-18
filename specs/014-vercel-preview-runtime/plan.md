# Implementation Plan: Vercel preview runtime

## Summary

The implementation adds a repo-owned Vercel PHP entrypoint, keeps Laravel writable state in temp storage, documents the supported local CLI preview workflow, and tracks the preview runtime as a separate feature from the GitHub Pages static export.

## Decisions

- Keep GitHub Pages static preview in place and add Vercel runtime preview as a second, more faithful preview surface
- Version the Vercel entrypoint in the repo instead of relying on ad-hoc staging folders
- Default preview runtime behavior to file-backed settings, cookie sessions, array cache, and SSR-disabled rendering
- Support local CLI deployments first rather than pretending a full Git-based production pipeline already exists

## Main changes

- Add `api/index.php`, `vercel.json`, and `.vercelignore`
- Bootstrap Laravel with temp storage, temp cache manifests, and SQLite temp-copy behavior when appropriate
- Document the runtime preview flow and its limits in README and architecture/AI docs
- Add a dedicated spec package and tracking entries for the new preview runtime feature

## Docs and tracking sync

- Specs updated: `spec.md`, `plan.md`, `tasks.md`
- Relevant docs updated: `README.md`, `Roadmap.md`, `docs/architecture/stack-decisions.md`, `docs/architecture/vercel-preview-runtime.md`, `docs/ai/public-static-handoff.md`, `CHANGELOG.md`
- Tracking maps updated in `docs/ai/`

## Validation

- `php artisan test`
- `npm run types:check`
- `npm run build`
- `php artisan route:list`
- local bootstrap check through `api/index.php`
