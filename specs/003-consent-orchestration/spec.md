---
linear_issue: TODO
github_project_item: TODO
github_project_status: validated
obsidian_note: docs/ai/obsidian/build-journal/2026-03-07-003-consent-orchestration.md
release: v0-foundation
title: Consent Orchestration
status: active
---

# Feature Specification: Consent Orchestration

Implement a reusable consent layer that keeps analytics off by default and blocks iframe-based media until users explicitly opt in.

## Problem

Privacy behavior becomes fragile when analytics and embeds are wired directly into UI components without a shared consent contract.

## Desired outcome

Consent should stay centralized, explicit, and ready for future adapters without forcing analytics into v0.

## In scope

- `necessary`, `analytics`, and `media` consent categories
- CookieConsent for preference capture
- IframeManager for media gating

## Out of scope

- Real analytics providers in v0
- Consent logic spread across unrelated components

## Constraints

- Keep `ANALYTICS_DRIVER=none`
- Keep media blocked until explicit opt-in

## Acceptance criteria

- Consent categories are limited to `necessary`, `analytics`, and `media`.
- CookieConsent manages preferences.
- IframeManager gates YouTube embeds.
- An internal registry exists for future scripts and embeds.

## Tracking

- Linear: keep the primary key in `linear_issue:`
- GitHub Project item: keep the board item key in `github_project_item:`
- GitHub Project status: mirror the board status in `github_project_status:`
- Obsidian note: `docs/ai/obsidian/build-journal/2026-03-07-003-consent-orchestration.md`
