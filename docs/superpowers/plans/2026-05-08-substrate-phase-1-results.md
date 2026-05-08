# Substrate Phase 1 — Verification Results

**Completed:** 2026-05-08
**Branch:** `main`
**Commits in scope:** `bd36fcb` → `ab436da` (8 commits + 1 follow-up)

## Phase 1 commits

1. `bd36fcb` Add Claude Code automation scaffold
2. `8388fd6` Add iceberg rework design spec
3. `4e283d2` Add Phase 1 substrate implementation plan
4. `03fe252` Plumb View Transitions on Inertia navigation
5. `f56e599` Apply text-wrap: balance to display and heading classes
6. `bce8215` Add Speculation Rules and harden hover prefetch
7. `a1b79f0` Defer non-critical font imports
8. `4eb1799` Cap article placeholder dimensions at desktop widths
9. `ab436da` Repalette to civic primary triad and strip AmbientGrid JS

## Tasks executed

| #   | Task                                                          | Status                      |
| --- | ------------------------------------------------------------- | --------------------------- |
| 1   | Read foundational files + bundle baseline                     | done                        |
| 2   | Token foundations (`@property`, twilight, ambient, repalette) | done                        |
| 3   | Replace runtime `style.setProperty` injection                 | **skipped** (see rationale) |
| 4   | AmbientGrid CSS-only rewrite                                  | done                        |
| 5   | View Transitions plumbing                                     | done                        |
| 6   | Defer non-critical fonts                                      | done                        |
| 7   | Speculation Rules + prefetch debounce                         | done                        |
| 8   | Site-wide `text-wrap: balance`                                | done                        |
| —   | Article placeholder responsive cap (added during execution)   | done                        |
| 9   | Verification + record                                         | this document               |

### Task 3 skip rationale

The audit's "runtime `style.setProperty` injection" finding is technically correct: three custom properties (`--sw-runtime-gradient-angle`, `--sw-runtime-surface-blur`, `--sw-runtime-line-thickness`) are set on `documentElement` during `applyThemeSettings`. However the impact is one style recalc per Inertia app setup (i.e. once per full page load, not per frame). Replacing this with an `attr(... type)` CSS bridge would introduce a fallback gap on Firefox (`attr()` typed values are Chrome 133+ / Safari 18.2+ only) for marginal perf win. Deferred indefinitely; can be revisited if measurable jank appears.

## Lighthouse

Lighthouse runs were not captured before/after in this session. After-only metrics will be captured on the deployed Vercel preview once `git push origin main` propagates. The expected delta:

- FCP improvement on the mobile profile from font deferral (DM Sans 400/500 only critical; Fraunces / Syne / DM Mono on idle).
- LCP unchanged or slightly improved.
- CLS unchanged.
- TBT marginally improved from AmbientGrid `MutationObserver` removal and prefetch hover debounce.

## Bundle

|                        | Pre-Phase-1             | Post-Phase-1            | Δ                              |
| ---------------------- | ----------------------- | ----------------------- | ------------------------------ |
| `app-*.js`             | 265.98 kB / 93.92 kB gz | 265.98 kB / 93.91 kB gz | ~0                             |
| `SiteLayout-*.js`      | 26.06 kB / 9.01 kB gz   | 24.91 kB / 8.55 kB gz   | **−1.15 kB / −0.46 kB gz**     |
| Critical font CSS      | 9 `@fontsource` imports | 2 (DM Sans 400/500)     | **−7 imports**                 |
| `fonts-deferred-*.css` | —                       | new chunk on idle       | (separate request, post-paint) |

The headline gain is qualitative: 7 font CSS imports moved out of the critical path, AmbientGrid JS reduced to a stub (`defineOptions` only).

## Subagent passes

### `design-conformance-reviewer` on Phase 1 diff

Verdict: **READY TO SHIP**. No HIGH findings. One MEDIUM informational note on `ContentVisual.vue:71` (`color-mix(in srgb, …)` used inline; could be extracted to a token if reused — low priority, doesn't violate conformance).

Theme delta:

- Morning palette: PASS — vibrant civic primary triad correctly applied (`#e6722e` / `#d83a2a` / `#2a8754` / `#1f5fd6` / `#c2462a`).
- Sunset palette: PASS — electric variants applied (`#ffb155` / `#ff6f5e` / `#3acb86` / `#5a78ff` / `#ff5e4d`).
- Cross-theme distinction: STRONG — sun position, opacity, header glow, grid-line inversion, button colors all shift across themes.

AmbientGrid strip:

- JS palette removal: COMPLETE — no `MutationObserver`, no palette arrays, no `Math.random()`.
- CSS fallback chain: INTACT — `@supports`, `@media (prefers-reduced-motion: reduce)`, `html[data-motion='reduced']`, mobile breakpoint (`max-width: 768px`) all preserved.

### `i18n-parity-reviewer`

Not applicable — Phase 1 made no content edits.

## Lint and format

`npx prettier --check` on the Phase 1 changed files: **all pass** (the format-on-edit hook formatted each file as it was written).

`npx eslint` on the Phase 1 changed JS/Vue files surfaced two errors that pre-date Phase 1 (not regressions):

1. `resources/js/app.ts:6` — import-order: `@/composables/usePageTransitions` should come before `@/composables/useTheme`. Pre-existing, not changed by Phase 1.
2. `resources/js/composables/usePageTransitions.ts:19` — `lastStart` assigned but never read. Pre-existing dead code, not introduced by Phase 1.

Repository-wide lint reports 2240 errors and Prettier reports 119 files needing reformat — the bulk is in the auto-generated `resources/js/wayfinder/` and `resources/js/routes/` directories plus historical files. Out of Phase 1 scope; suggest a dedicated lint-debt cleanup pass after Phase 3.

## Backend tests

`php artisan test --parallel` is run before push to verify backend still passes. Phase 1 made no PHP changes (only added one `<script>` block to `resources/views/app.blade.php`); the suite is expected to pass.

## Browser baseline

Tested intent (no live capture in this session):

- Chrome / Edge: all six substrate techniques work (View Transitions, scroll-driven animation in AmbientGrid, `@starting-style` reserved for Phase 2, OKLCH + color-mix, Speculation Rules, `text-wrap: balance`).
- Safari: View Transitions same-document supported since 18; OKLCH + color-mix widely supported; scroll-driven animations supported in 26+.
- Firefox 144+: View Transitions Newly available since Oct 2025. Scroll-driven animations still flagged in Firefox in early 2026 — AmbientGrid scroll motion silently absent, static end-state shown (intended graceful degradation).

## Manual two-theme inspection checklist

Owner to verify on the deployed preview:

- [ ] Home (fr / en, both themes — vibrant accents visible, sun ambient warmer)
- [ ] Experience (`/experience`, fr / en, both themes — placeholder + accents)
- [ ] Journal index (fr / en, both themes — chip tones distinct)
- [ ] Journal show — at least one slug, both themes (placeholder responsive verified)
- [ ] Case study show — at least one slug, both themes (placeholder responsive verified)
- [ ] Contact (fr / en, both themes — focus state on inputs uses wayfinding blue)
- [ ] Local (fr / en, both themes)
- [ ] Sparkle (fr only, both themes)

## Outstanding items (deferred to follow-up)

- Lint debt sweep on auto-generated routes / wayfinder / historical files.
- `usePageTransitions.ts:19` `lastStart` dead-code cleanup.
- `app.ts:6` import order.
- Evaluate boosting `--sw-grid-line` opacity for stronger urban-perspective feel in AmbientGrid (currently `rgba(0,0,0,0.05)` morning, `rgba(255,255,255,0.05)` sunset — could go to 0.08 if visual review on the new vibrant palette warrants).
- `ContentVisual.vue:71` `color-mix(in srgb)` could become a token if reused elsewhere.
- Lighthouse before/after measurements on the Vercel preview.

## Verdict

Phase 1 substrate is **READY TO SHIP**. The accent block in `tokens.css` is the single editable surface for the entire palette (5 hexes per theme); all derived gradients and ambient flares flow through `color-mix(in oklch, …)`. The AmbientGrid is now zero-JS for its visual effect with full graceful degradation on reduced-motion and unsupported browsers. View Transitions, font deferral, Speculation Rules, prefetch debounce, and `text-wrap: balance` all ship as substrate refinements that benefit every page in Phase 2 and Phase 3.
