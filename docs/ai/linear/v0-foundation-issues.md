# V0 Foundation Linear Issues

Copy these titles and descriptions directly into Linear for specs `001` to `004`.

## 001

**Title**

`[001] Repository Bootstrap`

**Description**

```md
Spec: `specs/001-repo-bootstrap/spec.md`
Roadmap block: `001-repo-bootstrap`
Release: `v0-foundation`
Obsidian note: `docs/ai/obsidian/build-journal/2026-03-07-001-repo-bootstrap.md`

## Summary

A GitHub Spec Kit-aligned repository foundation for a Laravel portfolio and reference implementation.

## Problem

The project needs a stable structure for application code, specs, docs, and workflow rules before feature work can scale.

## Desired outcome

The repo should boot locally on Windows, keep its docs/specs visible, and follow GitHub Spec Kit artifact conventions without pretending Codex has native `/speckit.*` commands.

## Acceptance criteria

- Laravel app boots locally with SQLite.
- README reflects the real setup.
- `AGENTS.md`, `docs/ai`, and repo-local skills exist.
- `.github` templates are in the correct path and naming shape.
```

## 002

**Title**

`[002] Content System`

**Description**

```md
Spec: `specs/002-content-system/spec.md`
Roadmap block: `002-content-system`
Release: `v0-foundation`
Obsidian note: `docs/ai/obsidian/build-journal/2026-03-07-002-content-system.md`

## Summary

Publish writing and case studies from versioned Markdown with explicit frontmatter, stable slugs, and publication-state filtering.

## Problem

The portfolio needs editorial content that is versioned with the codebase and shaped for routing, metadata, and release discipline.

## Desired outcome

Writing and case studies should behave like first-class app content, not loose Markdown files with ad-hoc rendering rules.

## Acceptance criteria

- Writing and case studies have index and detail pages.
- Draft filtering works.
- Invalid frontmatter causes test-visible failure.
- SEO metadata comes from the same content source.
```

## 003

**Title**

`[003] Consent Orchestration`

**Description**

```md
Spec: `specs/003-consent-orchestration/spec.md`
Roadmap block: `003-consent-orchestration`
Release: `v0-foundation`
Obsidian note: `docs/ai/obsidian/build-journal/2026-03-07-003-consent-orchestration.md`

## Summary

Implement a reusable consent layer that keeps analytics off by default and blocks iframe-based media until users explicitly opt in.

## Problem

Privacy behavior becomes fragile when analytics and embeds are wired directly into UI components without a shared consent contract.

## Desired outcome

Consent should stay centralized, explicit, and ready for future adapters without forcing analytics into v0.

## Acceptance criteria

- Consent categories are limited to `necessary`, `analytics`, and `media`.
- CookieConsent manages preferences.
- IframeManager gates YouTube embeds.
- An internal registry exists for future scripts and embeds.
```

## 004

**Title**

`[004] SEO Foundation`

**Description**

```md
Spec: `specs/004-seo-foundation/spec.md`
Roadmap block: `004-seo-foundation`
Release: `v0-foundation`
Obsidian note: `docs/ai/obsidian/build-journal/2026-03-07-004-seo-foundation.md`

## Summary

Provide canonical tags, metadata, JSON-LD, sitemap, and robots without requiring full SSR.

## Problem

If SEO stays an afterthought, the first request can miss canonical structure, machine-readable metadata, and crawlable discovery endpoints.

## Desired outcome

The public site should serve consistent metadata and machine-readable SEO outputs from Laravel before SSR becomes a mandatory runtime.

## Acceptance criteria

- Public pages expose canonical URLs and descriptions server-side.
- Writing and case studies render article JSON-LD.
- `robots.txt` and `sitemap.xml` are generated from app data.
- No multilingual metadata is emitted in v0.
```
