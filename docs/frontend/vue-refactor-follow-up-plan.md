# Vue Refactor Follow-Up Plan

This follow-up is intentionally deferred until the current feature work lands.

## Why this is deferred

- The current feature branch should stay focused on operator-facing behavior.
- A broad Vue refactor would mix architecture cleanup with runtime changes and make review harder.
- The public and admin surfaces now have enough real implementation detail to refactor against concrete usage, not guesses.

## Goal

Reduce oversized `.vue` files, remove repeated markup and logic, externalize page-scoped CSS where it improves clarity, and make component boundaries easier to explain and maintain.

## Constraints

- Keep the existing design system and visual behavior stable.
- Preserve Inertia and SSR compatibility.
- Do not use the refactor as a stealth redesign.
- Prefer incremental extraction over a big-bang rewrite.
- Keep the repo English-only.

## Refactor principles

1. Extract repeated UI patterns before introducing new abstractions.
2. Keep domain logic close to the feature until the reuse case is real.
3. Move CSS out of large page files only when the resulting ownership is clearer.
4. Prefer small, named building blocks over generic catch-all components.
5. Keep route, SEO, and validation behavior unchanged unless a bug is being fixed.

## Phase 1: Inventory and hotspots

- Audit the largest public and admin `.vue` files by size and responsibility.
- Mark repeated patterns:
  - section headers
  - settings fields and help blocks
  - CTA clusters
  - proof cards
  - locale-aware utility copy
- Identify CSS that is page-local but large enough to justify extraction.

## Phase 2: Admin extraction first

- Split the admin settings page into smaller blocks:
  - settings header/status
  - section rail
  - grouped field panels
  - toggle cards
- Extract shared settings field primitives only where repetition is clear.
- Move admin page CSS into an external stylesheet or feature-scoped CSS module structure if it reduces file size without hiding ownership.

## Phase 3: Public page extraction

- Refactor the heaviest public pages next:
  - `Home`
  - `Experience`
  - `Projects`
  - `Contact`
- Extract repeated credibility/conversion blocks into focused components.
- Keep markdown-backed content flow and locale behavior unchanged.

## Phase 4: Shared composables and helpers

- Extract repeated local state only when it appears in at least two real places.
- Good candidates:
  - locale-aware UI labels
  - CV/download CTA helpers
  - grouped metadata formatting
  - admin dirty-state helpers

## Phase 5: CSS externalization

- Move large page `<style scoped>` blocks into external files only when:
  - the page file is materially shorter afterward
  - the CSS ownership remains obvious
  - naming stays feature-scoped
- Keep tokens and motion variables in the current system.

## Phase 6: Validation and review

- Run the standard runtime checks after each extraction step:
  - `git diff --check`
  - `npm run types:check`
  - `npm run build`
  - `php artisan route:list`
  - `php artisan test`
  - `composer run ci:check`
- Prefer multiple reviewable refactor commits over one large rewrite.

## Recommended rollout on `main`

1. Merge the current feature PR.
2. Create a fresh branch from updated `main`.
3. Start with admin settings extraction.
4. Land one reviewable refactor checkpoint.
5. Continue with the heaviest public pages only after the admin pass is stable.

## Definition of done

- Large page files are meaningfully shorter.
- Repeated structures are extracted into clearly named components.
- CSS ownership is easier to follow.
- No public routing, locale, SEO, or settings behavior regresses.
- The refactor is explainable in an interview as maintainability work driven by real duplication.
