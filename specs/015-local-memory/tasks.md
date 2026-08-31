# Tasks: Local Memory

## Implementation

- [ ] `useLocalMemory` composable: guarded storage, visit snapshot read once
      and written forward, per-slug reading positions
- [ ] Journal index: `data-new` on entries published after the snapshot, badge
      element, CSS-driven visibility
- [ ] Article: progress rail on `animation-timeline: scroll()`, no listener
- [ ] Article: resume invitation, dismissible, never scrolls on its own
- [ ] Bilingual copy for every new string, keys sorted

## Documentation

- [ ] `docs/rgpd/analytics-modes.md`: name this as client-only state so it is
      not later mistaken for analytics
- [ ] `docs/style/motion.md`: the scroll rail
- [ ] `docs/style/components.md`: the two new surfaces

## Validation

- [ ] `npm run check`, `composer run lint:check`, `php artisan test`
- [ ] Both themes, both breakpoints
- [ ] First visit marks nothing; second visit marks only what is new
- [ ] Marks survive a reload
- [ ] Storage throwing: pages render, console clean
- [ ] Progress rail registers no scroll listener

## Handoff

- [ ] Note in the PR what a reader can check in devtools to verify the claim
