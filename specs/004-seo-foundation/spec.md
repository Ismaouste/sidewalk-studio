---
linear_issue:
title: SEO Foundation
status: active
---

# Feature Specification: SEO Foundation

Provide canonical tags, metadata, JSON-LD, sitemap, and robots without requiring full SSR.

## Problem

If SEO stays an afterthought, the first request can miss canonical structure, machine-readable metadata, and crawlable discovery endpoints.

## Desired outcome

The public site should serve consistent metadata and machine-readable SEO outputs from Laravel before SSR becomes a mandatory runtime.

## In scope

- Backend-generated metadata and canonical URLs
- JSON-LD for pages and articles
- `robots.txt` and `sitemap.xml`

## Out of scope

- Mandatory SSR runtime for normal development
- Multilingual SEO in v0

## Constraints

- Keep SEO generated from app data
- Preserve SSR compatibility without requiring it day-to-day

## Acceptance criteria

- Public pages expose canonical URLs and descriptions server-side.
- Writing and case studies render article JSON-LD.
- `robots.txt` and `sitemap.xml` are generated from app data.
- No multilingual metadata is emitted in v0.

## Tracking

- Linear: keep the primary key in `linear_issue:`
- Cross-tool mirrors live under `docs/ai/`
