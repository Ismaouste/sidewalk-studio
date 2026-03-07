---
linear_issue: TODO
github_project_item: TODO
github_project_status: in_progress
obsidian_note: docs/ai/obsidian/build-journal/2026-03-07-010-admin-shell-and-auth.md
release: post-v0
title: Admin Shell and Auth
status: active
---

# Feature Specification: Admin Shell and Auth

A minimal protected operator surface for Sidewalk Studio that can host the site settings editor without expanding into a full multi-user back office.

## Problem

The repository now has a bounded `site_settings` domain with typed reads and a validated write contract, but there is no protected runtime boundary to expose that write path safely. Adding public routes or improvised local-only controls would weaken the architecture and blur the difference between application runtime and operator tooling.

## Desired outcome

The application should gain a minimal authenticated admin shell that protects operator-only screens and can mount the first site settings editor from `009-admin-site-settings`.

## In scope

- A single protected admin route group under `/admin`
- Session-based authentication suitable for one operator or a very small team
- A minimal login/logout flow using Laravel-native primitives
- A reusable admin layout shell with space for settings screens and future internal tools
- The first protected mounting point for the site settings editor

## Out of scope

- Multi-role permission systems
- Rich editorial workflow for writing or case studies
- Social login, magic links, or SSO
- Audit logging beyond what Laravel already exposes by default
- A full dashboard with analytics, charts, or business metrics

## Constraints

- Keep the local-first workflow intact on Windows
- Do not introduce external auth providers or paid services
- Keep the admin surface server-rendered through existing Laravel + Inertia patterns
- Preserve the public shell and do not mix public navigation with admin navigation
- Require explicit route protection before any settings write UI is reachable

## Route and UI boundary

- `/admin/login` should remain publicly reachable only for authentication.
- `/admin` and nested routes should require an authenticated session.
- The admin shell should use a dedicated layout with its own navigation and page title structure.
- Public pages must not expose admin links or auth state beyond the existing shared `auth.user` prop.

## Authentication expectations

- Start with one simple operator model based on the existing Laravel user table and session guard.
- Use rate limiting and standard validation on login.
- Keep password reset and registration out of scope unless the repo already ships them.
- If a bootstrap path is needed for the first operator account, define it as an explicit local-only command or seeder instead of an open registration screen.

## Dependency on `009-admin-site-settings`

- `009-admin-site-settings` now provides the bounded settings aggregate and service-level write contract.
- `010-admin-shell-and-auth` should mount a protected settings page on top of `SiteSettingsService::current()` and `SiteSettingsService::update()`.
- No additional persistence layer should be introduced for the first admin settings screen.

## Acceptance criteria

- A user can authenticate locally and establish a protected session.
- `/admin` routes are inaccessible when unauthenticated and redirect to the login boundary.
- The admin shell layout is distinct from the public shell and ready to host multiple operator pages later.
- The first protected settings page can read and submit the bounded `site_settings` payload without exposing public write access.
- Logout clears the protected session and returns the user to a safe public route.

## Tracking

- Linear: keep the primary issue key in `linear_issue:`
- GitHub Project item: keep the board item key in `github_project_item:`
- GitHub Project status: mirror the board status in `github_project_status:`
- Obsidian note: `docs/ai/obsidian/build-journal/2026-03-07-010-admin-shell-and-auth.md`
