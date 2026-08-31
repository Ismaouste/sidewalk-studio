# Tasks: Local Memory

## Implementation

- [x] `useLocalMemory` composable: guarded storage, visit snapshot read once
      and written forward, per-slug reading positions
- [x] Journal index: `data-new` on entries published after the snapshot, badge
      element, CSS-driven visibility
- [x] Article: progress rail on `animation-timeline: scroll()`, no listener —
      already shipped in `ArticleShowLayout`, verified rather than built
- [x] Article: resume invitation, dismissible, never scrolls on its own
- [x] Bilingual copy for every new string, keys sorted

## Documentation

- [x] `docs/rgpd/analytics-modes.md`: name this as client-only state so it is
      not later mistaken for analytics
- [x] `docs/style/motion.md`: the scroll rail
- [x] `docs/style/components.md`: the two new surfaces

## Validation

- [x] `npm run check`, `composer run lint:check`, `php artisan test`
- [x] Both themes, both breakpoints
- [x] First visit marks nothing; second visit marks only what is new
- [x] Marks survive a reload
- [x] Storage throwing: pages render, console clean
- [x] Progress rail registers no scroll listener

## Handoff

- [x] Note in the PR what a reader can check in devtools to verify the claim
