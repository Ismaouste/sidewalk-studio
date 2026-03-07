---
linear_issue:
title: Content System
status: active
---

# Feature Specification: Content System

Publish writing and case studies from versioned Markdown with explicit frontmatter, stable slugs, and publication-state filtering.

## Problem

The portfolio needs editorial content that is versioned with the codebase and shaped for routing, metadata, and release discipline.

## Desired outcome

Writing and case studies should behave like first-class app content, not loose Markdown files with ad-hoc rendering rules.

## In scope

- Markdown content under `resources/content/`
- Explicit frontmatter validation
- Public index/detail routes for writing and case studies

## Out of scope

- External CMS integration
- Rich editorial tooling beyond the repo workflow

## Constraints

- Use the repo-local content model and keep docs updated if it changes

## Acceptance criteria

- Writing and case studies have index and detail pages.
- Draft filtering works.
- Invalid frontmatter causes test-visible failure.
- SEO metadata comes from the same content source.

## Tracking

- Linear: keep the primary key in `linear_issue:`
- Cross-tool mirrors live under `docs/ai/`
