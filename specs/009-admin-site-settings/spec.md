---
linear_issue: TODO
github_project_item: TODO
github_project_status: proposed
obsidian_note: docs/ai/obsidian/build-journal/2026-03-07-009-admin-site-settings.md
release: post-v0
title: Admin Site Settings
status: proposed
---

# Feature Specification: Admin Site Settings

A bounded runtime settings domain for non-secret site configuration that a future protected admin surface can manage.

## Problem

The current site configuration is split between environment variables, static config files, and Markdown content. That is acceptable for v0, but it makes routine updates to global site settings harder than they need to be once the product starts moving more often. It also risks blurring secrets, infrastructure config, and ordinary runtime content if the next step is not defined clearly.

## Desired outcome

The application should gain a single `site_settings` source for bounded non-secret runtime configuration such as site identity, contact details, social links, SEO defaults, consent copy, and non-sensitive feature toggles. Public pages should consume those values through one service, while the future write UI remains a separate concern that can sit behind `010-admin-shell-and-auth`.

## In scope

- A bounded settings domain that a future protected admin area can manage
- Typed validation and a single application-facing settings service
- SQLite-first persistence that stays migration-friendly for a later PostgreSQL path
- Default bootstrapping from the current config and env-backed values

## Out of scope

- Full CMS behavior for writing or case-study content
- Full admin shell, authentication, and role management
- Multi-user roles, permissions, or editorial workflow
- Admin-managed API keys, secret rotation, or encrypted secret storage
- External API integrations or third-party back-office tools
- A loose arbitrary key-value store for unknown future settings

## Constraints

- Keep the repo local-first on Windows
- Keep SQLite as the default persistence layer when the feature lands
- Do not force a server database migration before there is a concrete product need
- Keep `.env` as the source of truth for secrets, credentials, and infrastructure/runtime config
- Keep public read access behind one service instead of direct model or config lookups

## Settings groups

The first implementation should limit itself to these groups:

- `site_identity`
  Site name, tagline, and long description
- `contact_details`
  Public email, location, and availability text
- `social_links`
  Non-secret public profile links
- `seo_defaults`
  Default SEO title suffix, description, and social metadata fallbacks
- `consent_copy`
  Non-secret consent and preferences copy that may need content updates later
- `feature_toggles`
  Non-sensitive booleans or enums for bounded runtime behavior

## Source-of-truth boundary

- `.env` remains for secrets, credentials, infrastructure/runtime config, DB connection, app key, provider keys, mail config, and similar sensitive values.
- `site_settings` is for bounded non-secret runtime configuration that may change without a deploy.
- If the product later needs admin-managed secrets, they must use a separate encrypted store instead of expanding `site_settings`.
- Markdown content remains the source of truth for writing and case studies.

## Persistence expectations

- Start with a single bounded `site_settings` aggregate rather than a generic key-value table.
- Use typed groups and explicit validation rules.
- A practical first shape is one row with grouped JSON columns or equivalent typed casts for:
  `site_identity`, `contact_details`, `social_links`, `seo_defaults`, `consent_copy`, and `feature_toggles`.
- Public callers must not read raw persistence structures directly.

## Cache expectations

- Read access should go through one settings service that caches the hydrated settings payload.
- Writes should invalidate or refresh the cache immediately.
- If no persisted row exists yet, the service should fall back to default bootstrapped values safely.

## Validation and bootstrap expectations

- The first seed or bootstrap path should create a valid initial settings payload from the current config and env-backed defaults.
- The bootstrap must be idempotent: rerunning it must not overwrite intentional operator changes.
- Validation should reject missing required fields, malformed URLs, and invalid toggle values before persistence.

## Public consumption model

- Public pages, shared Inertia props, metadata builders, and later consent copy readers should consume settings through one application service.
- Controllers and support classes should not duplicate fallback logic or query settings persistence directly.

## Acceptance criteria

- A bounded `site_settings` domain exists for non-secret runtime configuration and does not replace `.env`.
- Secret values and API keys are explicitly excluded from the first `site_settings` scope.
- The first settings groups are explicitly defined and validated.
- Bootstrap defaults can hydrate the settings domain from the current config and env-backed values without overwriting later edits.
- Public pages read managed settings through one service instead of scattered config lookups.
- The read path supports cache invalidation or refresh after writes.
- The write path is defined so `010-admin-shell-and-auth` can mount a protected UI on top of the same settings domain.

## Tracking

- Linear: keep the primary issue key in `linear_issue:`
- GitHub Project item: keep the board item key in `github_project_item:`
- GitHub Project status: mirror the board status in `github_project_status:`
- Obsidian note: `docs/ai/obsidian/build-journal/2026-03-07-009-admin-site-settings.md`
