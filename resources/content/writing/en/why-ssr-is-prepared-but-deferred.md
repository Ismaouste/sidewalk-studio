---
title: Why SSR Is Prepared but Deferred
slug: why-ssr-is-prepared-but-deferred
summary: The repo keeps a path to SSR without paying the operational cost of an SSR runtime in the first local-first milestone.
status: published
published_at: 2026-03-07
updated_at: 2026-03-07
tags:
    - ssr
    - inertia
    - strategy
    - notes-dev
category: journal
accent_tone: violet
seo_title: Why SSR Is Prepared but Deferred
seo_description: Sidewalk Studio stays compatible with SSR later, but avoids adding the runtime complexity before the first content and SEO foundations exist.
---

SSR is useful, but it is not free.

For this v0, the project keeps the SSR entrypoint and avoids architecture choices that would block later activation. That is different from enabling SSR immediately.

The reason is practical: the first milestone is about system boundaries, content modeling, and privacy-safe orchestration. Adding an SSR runtime too early would expand the moving parts before the basic content and SEO model is stable.

The repo therefore chooses a middle path:

- keep the SSR file and build affordance in place
- implement server-supplied metadata for the first request
- defer the full SSR runtime to a later spec once the information architecture is settled
