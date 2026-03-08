---
title: Repository Bootstrap for a Spec-Driven Portfolio
slug: repo-bootstrap-foundation
summary: How the repository moved from an empty public shell into a Laravel-first workspace with explicit specs, operational docs, and enough structure to support real follow-up work.
status: published
published_at: 2026-03-07
updated_at: 2026-03-07
tags:
    - laravel
    - architecture
    - spec-driven
seo_title: Repository Bootstrap for a Spec-Driven Portfolio
seo_description: 'A walkthrough of the first Sidewalk Studio milestone: repo normalization, Laravel bootstrap, and spec-first project framing.'
client: Sidewalk Studio
role: Product scoping, architecture, and implementation
stack:
    - Laravel 12
    - Inertia.js
    - Vue 3
    - TypeScript
    - Tailwind CSS
outcomes:
    - Replaced a placeholder repo with a working Laravel and Inertia application
    - Moved governance, specs, and AI-operational context into explicit reviewable locations
    - Created a base that could support later locale, admin, SEO, and consent work without structural reset
---

The first iteration of Sidewalk Studio started from a mismatch: the repository ambition was already clear, but the tracked codebase only contained a `README.md` and a `LICENSE`.

That is a small starting point on paper, but it creates a real delivery risk. Without a usable application boundary and a readable project structure, every later feature has to solve the bootstrap again while also solving its own problem.

The immediate goal was not to chase surface polish. It was to create a repository that could support a real engineering workflow:

- a Laravel application that boots locally on Windows
- a visible specification system
- documentation that explains decisions instead of only listing files
- a place for future AI-operational context and reusable skills

That meant treating the bootstrap itself as a feature with delivery constraints, not a throwaway setup step.

## Situation and constraints

The repository had to become useful quickly without drifting into framework theater.

The main constraints were straightforward:

- local-first development had to remain the default
- the stack had to stay readable for future review and hiring conversations
- documentation and specs had to be first-class outputs, not backlog debris
- the result had to support later public proof, not just local experimentation

## What changed

The repository was normalized around three layers.

### 1. Application boundary

An official Laravel 12 + Inertia + Vue starter kit became the baseline. This gave the project a current stack without spending the first day manually recreating framework plumbing.

### 2. Specification boundary

The constitution moved under `.specify/memory`, while feature work gained a top-level `specs/` directory. That split matters: tool memory stays hidden and operational, while approved feature specs stay readable and reviewable.

### 3. Documentation and operational context

Architecture notes moved under `docs/architecture`, and repo-level AI conventions gained a dedicated home instead of being buried in ad-hoc prompt snippets.

## Why this matters technically

A portfolio repo should prove judgment. The first proof point is not visual flair. It is whether the repo can explain itself, grow cleanly, and survive the second feature without turning into a pile of disconnected experiments.

This bootstrap proved three things early:

- the repo could carry implementation, specs, and docs together without one of them becoming fake
- later features could build on explicit boundaries instead of invisible assumptions
- the public site could become a credible engineering surface because the repository itself had already become legible
