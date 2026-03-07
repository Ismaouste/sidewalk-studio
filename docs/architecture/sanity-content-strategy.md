# Sanity Content Strategy

## Decision summary

- Do not adopt Sanity as a live content source now.
- Keep core public pages code-driven.
- Keep Writing and Case Studies repo-versioned for the current release line.
- Revisit Sanity later only as a bounded content layer for one editorial domain at a time.
- If Sanity is adopted later, introduce it through a thin application adapter instead of wiring it directly into page components.

## Why this is not a CMS pass

The current public site already has a coherent architecture:

- core public pages are implemented in code and shaped through Laravel + Inertia
- Writing and Case Studies already have a stable Markdown contract
- SEO, sitemap, and publication rules reuse the same local content source
- `site_settings` already covers bounded runtime configuration without turning the app into a remote-managed shell

Replacing that with a remote CMS now would increase moving parts without solving a current delivery problem.

## What should stay code-driven

These domains should remain application-owned for now:

- `Home`
- `Experience`
- `Local`
- `Contact`
- public routing and information architecture
- navigation structure and CTA placement
- SEO composition logic, canonical generation, sitemap generation, and JSON-LD assembly
- design-system primitives, shell behavior, and theme wiring

These areas are closer to product architecture than editorial entry management. They benefit from code review, tests, and release discipline more than from CMS editing.

## What should stay repo-versioned Markdown

These collections should remain in `resources/content/` for now:

- Writing
- Case Studies

Reasons:

- they are tightly coupled to release notes, code changes, and technical proof
- frontmatter validation is already part of the app contract
- local versioning is useful for editorial traceability
- the current quantity is still small and maintainable
- moving them now would create migration work before there is a demonstrated authoring need

## What Sanity could plausibly support later

Sanity is a reasonable future candidate for content that benefits from structured remote authoring but does not need to own the whole site.

Good future candidates:

- a new `local_notes` or `local_journal` collection linked from the Local page
- a future higher-volume editorial stream that is adjacent to Writing but not identical to the current repo-backed archive
- curated project metadata such as spotlight flags, short summaries, gallery/media references, or optional supporting links
- richer modular content blocks if a later editorial workflow genuinely needs them

Bad candidates right now:

- `site_settings`
- secrets, API keys, mail config, analytics config, or infrastructure settings
- the current core page bodies for `Home`, `Experience`, `Local`, and `Contact`
- a forced replacement of all Markdown content

## Recommended adoption boundary

If Sanity is explored later, the boundary should be:

- application code remains responsible for routing, SEO policy, consent policy, and shell composition
- Sanity provides structured content payloads for one bounded editorial domain
- page components consume application-shaped view models, not raw Sanity documents

That means the adapter belongs in Laravel application services, not in Vue pages.

## Least risky adoption path

1. Keep the current architecture as-is through the current public/content polish phase.
2. Revisit Sanity only when one of these becomes true:
   - editorial volume grows enough that repo-only authoring becomes friction
   - non-technical contributors need a safer editing surface
   - media-heavy content starts to justify a dedicated structured asset workflow
3. If that threshold is reached, start with one new collection only.
4. Add a thin source boundary in PHP for that collection before touching any existing Markdown collection.
5. Keep Writing and Case Studies local until the first Sanity-backed collection has proved its value.

## Preferred first candidate

The preferred first candidate is a new `local_notes` collection, not the existing Writing archive.

Why:

- it is editorially distinct from the core Local page
- it can validate remote authoring without rewriting a high-value existing collection
- it avoids dual-source complexity inside Writing and Case Studies
- it gives Sanity a real but low-risk job: short structured entries, optional media, and future categorization by place or topic

## What to avoid

- bootstrapping a full Sanity Studio in this repo before there is a real adoption decision
- dual-source rendering for the same collection
- replacing Markdown and Sanity with each other in an all-or-nothing way
- moving public routing or metadata ownership into Sanity
- wiring preview, live editing, or draft-mode behavior into production pages now
- coupling consent or analytics settings to remote content infrastructure

## Future implementation shape if adopted later

The first code step should be small:

- add a source interface for one editorial domain
- keep the current Markdown repository as the default implementation
- add a second implementation for Sanity only when a real pilot collection exists
- normalize all content into the same app-level shape before controllers render it

This repo does not need that abstraction yet.

## Official Sanity references checked

- Content Lake overview: https://www.sanity.io/docs/content-lake
- JavaScript client getting started: https://www.sanity.io/docs/apis-and-sdks/js-client-getting-started
- Querying with the JavaScript client: https://www.sanity.io/docs/apis-and-sdks/js-client-querying
- Schema types and helpers: https://www.sanity.io/docs/schema-types
- Type generation: https://www.sanity.io/docs/sanity-typegen
- API versioning: https://www.sanity.io/docs/api-versioning
