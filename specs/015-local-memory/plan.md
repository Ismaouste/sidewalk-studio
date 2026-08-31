# Implementation Plan: Local Memory

## Summary

One small composable owns every read and write, so the storage guard exists in
one place and the two features cannot drift on how they handle a browser that
refuses storage. Everything else is presentation: the journal marks entries
from a set computed once at hydration, and the article page renders a progress
rail that no JavaScript ever touches.

## Decisions

- **A snapshot, not a live value.** The visit timestamp is read once when the
  module first loads and kept in memory for the session, then immediately
  written forward. If the marks were derived from the stored value directly
  they would vanish on the first reload, which reads as a bug rather than as a
  feature.
- **The first visit marks nothing.** With no stored timestamp every entry is
  newer than nothing, so marking them all would be noise on exactly the visit
  where the reader has the least context. The timestamp is written and the
  marks start on the second visit.
- **Progress rail on `animation-timeline: scroll()`.** The rail is a scaled
  element on the document scroll timeline, so it runs off the main thread and
  needs no listener. Under `@supports not` it renders at zero width and simply
  does not appear.
- **The resume invitation never scrolls on its own.** Being moved without
  asking is disorienting and undoes the reader's own scroll. It is an offer,
  dismissible, and it disappears once acted on.
- **Marks are real elements, not `::after` content.** The decision of which
  entries are new has to be JavaScript because it reads storage; the badge
  itself is a rendered element toggled by CSS on a `data-new` attribute.
  Generated content is announced inconsistently by screen readers, so a label
  that carries meaning should not live in `content:`.
- **A minimum depth for resuming.** Offering to restore a position two screens
  from the top of a long article is useful; offering to restore the top of it
  is noise.

## Main changes

- `resources/js/composables/useLocalMemory.ts` — the storage boundary: the
  visit snapshot, the per-slug positions, and the guards.
- `resources/js/pages/Writing/Index.vue` — `data-new` on entries published
  after the snapshot, plus the badge.
- `resources/js/components/layout/ArticleShowLayout.vue` — the scroll rail and
  the resume invitation.
- `resources/css/tokens.css` — only if the rail needs a token that does not
  exist yet.
- `resources/js/copy/{en,fr}/pages/writingIndex.ts` and `writingShow.ts` — the
  new strings, keys sorted, French checked against English by `satisfies`.

## Docs and tracking sync

- Specs updated: `spec.md`, `plan.md`, `tasks.md`
- `docs/rgpd/analytics-modes.md` gains a note: this is client-only state, it is
  not analytics, and it is worth naming so it is not mistaken for it later
- `docs/style/motion.md` gains the scroll rail
- `docs/style/components.md` gains the two new surfaces

## Validation

- `npm run check`
- `composer run lint:check`
- `php artisan test`
- Browser: both themes, both breakpoints, storage available and storage
  throwing, first visit and returning visit
