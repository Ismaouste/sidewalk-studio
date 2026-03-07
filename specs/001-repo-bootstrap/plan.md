# Implementation Plan: Repository Bootstrap

## Summary

Keep Laravel 12 + Inertia + Vue as the base, normalize the repo around GitHub Spec Kit-compatible artifacts, and defer infrastructure work that is not needed for v0.

## Decisions

- Keep SQLite for the local-first v0
- Keep `specs/` top-level while storing Spec Kit memory and templates under `.specify/`

## Main changes

- Bootstrap the Laravel app and public shell
- Add repo governance, docs, and spec files
- Reserve `.specify/scripts/` for future official Spec Kit script generation

## Docs and tracking sync

- README, roadmap, and architecture docs aligned
- AI workflow docs aligned with the current Codex execution model

## Validation

- `php artisan test`
- `composer run ci:check`
- `npm run build`
