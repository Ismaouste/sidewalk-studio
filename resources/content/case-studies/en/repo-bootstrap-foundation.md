---
title: Repository Bootstrap for a Spec-Driven Portfolio
slug: repo-bootstrap-foundation
summary: How the repo was normalized from a thin README into a Laravel-first workspace with specs, docs, and AI-operational scaffolding.
status: draft
published_at: 2026-03-07
updated_at: 2026-03-07
tags:
    - laravel
    - architecture
    - spec-driven
    - work-sample
category: work
accent_tone: dominant
seo_title: Repository Bootstrap for a Spec-Driven Portfolio
seo_description: 'A walkthrough of the first Sidewalk Studio milestone: repo normalization, Laravel bootstrap, and spec-first project framing.'
client: Sidewalk Studio
role: Product, architecture, implementation
stack:
    - Laravel 12
    - Inertia.js
    - Vue 3
    - TypeScript
    - Tailwind CSS
outcomes:
    - Established a stable local-first scaffold
    - Moved governance and constitution into explicit locations
    - Created the first feature spec folders and roadmap shape
---

The first iteration of Sidewalk Studio started from a mismatch: the repository ambition was already clear, but the tracked codebase only contained a `README.md` and a `LICENSE`.

The immediate goal was not to chase surface polish. It was to create a repo that could support a real engineering workflow:

- a Laravel application that boots locally on Windows
- a visible specification system
- documentation that explains decisions instead of only listing files
- a place for future AI-operational context and reusable skills

That meant treating the bootstrap itself as a feature.

## What changed

The repository was normalized around three layers.

### 1. Application layer

An official Laravel 12 + Inertia + Vue starter kit became the baseline. This gave the project a current stack without spending the first day manually recreating framework plumbing.

### 2. Specification layer

The constitution moved under `.specify/memory`, while feature work gained a top-level `specs/` directory. That split matters: tool memory stays hidden and operational, while approved feature specs stay readable and reviewable.

### 3. Documentation and AI layer

Architecture notes moved under `docs/architecture`, and repo-level AI conventions gained a dedicated home instead of being buried in ad-hoc prompt snippets.

## Why it matters

A portfolio repo should prove judgment. The first proof point is not visual flair. It is whether the repo can explain itself, grow cleanly, and survive the second feature without turning into a pile of disconnected experiments.
