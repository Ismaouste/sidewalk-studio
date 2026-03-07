---
linear_issue:
github_project_item:
github_project_status: proposed
obsidian_note: docs/ai/obsidian/build-journal/2026-03-07-009-admin-site-settings.md
release: post-v0
title: Admin Site Settings
status: proposed
---

# Feature Specification: Admin Site Settings

A protected internal settings surface for site-wide metadata and bounded runtime content.

## Problem

The current site configuration is split between environment variables, static config files, and Markdown content. That works for v0, but it makes routine updates to global site settings harder than they need to be once the product starts moving more often.

## Desired outcome

The application should expose a small protected admin surface where a trusted operator can manage stable site settings such as contact details, social links, SEO defaults, consent copy, and future feature toggles without turning the repo into a CMS.

## In scope

- A protected admin area for a bounded set of site-wide settings
- Typed validation and a single application-facing settings service
- SQLite-first persistence that stays migration-friendly for a later PostgreSQL path

## Out of scope

- Full CMS behavior for writing or case-study content
- Multi-user roles, permissions, or editorial workflow
- External API integrations or third-party back-office tools

## Constraints

- Keep the repo local-first on Windows
- Keep SQLite as the default persistence layer when the feature lands
- Do not force a server database migration before there is a concrete product need

## Acceptance criteria

- A protected internal route exists for managing the bounded settings set.
- Public pages read the managed settings through one application service instead of scattered config lookups.
- Validation prevents incomplete or malformed settings from being saved.
- Existing Markdown content and environment bootstrap rules remain intact unless explicitly replaced by the new settings source.

## Tracking

- Linear: keep the primary issue key in `linear_issue:`
- GitHub Project item: keep the board item key in `github_project_item:`
- GitHub Project status: mirror the board status in `github_project_status:`
- Obsidian note: `docs/ai/obsidian/build-journal/2026-03-07-009-admin-site-settings.md`
