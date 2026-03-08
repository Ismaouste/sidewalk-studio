---
title: Consent Orchestration Before Analytics
slug: consent-orchestration-before-analytics
summary: Why the consent system was built as a reusable gating layer before any analytics provider was connected, so privacy stayed a boundary instead of leaking through the app.
status: published
published_at: 2026-03-07
updated_at: 2026-03-07
tags:
    - privacy
    - consent
    - architecture
seo_title: Consent Orchestration Before Analytics
seo_description: Building the consent layer first keeps analytics providers optional and prevents privacy logic from leaking through the app.
client: Sidewalk Studio
role: Product architecture and frontend implementation
stack:
    - CookieConsent
    - IframeManager
    - TypeScript
    - Inertia.js
outcomes:
    - Fixed the public consent contract to three explicit categories for v0
    - Added script and embed orchestration surfaces without hardwiring a provider
    - Deferred analytics provider choice without delaying the privacy-safe UI and routing work
---

This project needs analytics later, but it does not need analytics first.

That distinction is important. If Matomo or PostHog enters the codebase before a clear consent contract exists, the repo starts teaching the wrong lesson: privacy becomes an integration detail instead of a system boundary.

## Situation and constraint

The requirement was not theoretical compliance language. The public site already needed consent-aware behavior around embeds, future analytics, and operator-controlled copy.

The constraint was to solve that without:

- locking the project to one analytics vendor
- scattering privacy logic across random components
- pretending an analytics integration already existed

## The rule

For v0, the consent model exposes only three categories:

- `necessary`
- `analytics`
- `media`

That keeps the public contract explicit and small. It is enough to cover cookies, no-op analytics placeholders, and iframe-based embeds.

## The implementation choice

The frontend combines two responsibilities:

- `CookieConsent` manages user preferences, persistence, and the modal UX.
- `IframeManager` prevents third-party embeds from loading until the matching category is accepted.

An internal registry sits between them so future providers do not have to know anything about the UI layer.

## Why this is better than wiring a provider directly

It keeps provider churn cheap.

A later analytics spec can add Matomo or PostHog adapters without rethinking the consent categories, footer actions, or media gating model. The compliance logic stays readable because it is centralized.

## What this proved

This was one of the first places where Sidewalk Studio stopped being a design shell and became a product system.

It proved that:

- privacy could be modeled as infrastructure, not as last-minute copy
- the frontend could keep a calm public UX while still enforcing real gating
- future integrations could stay optional because the boundary was defined first
