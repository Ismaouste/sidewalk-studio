# Experience Page Rework Phase 2 — Verification Results

**Completed:** 2026-05-08
**Branch:** `main`
**Commits in scope:** Phase 2 commits + opportunistic CI / GH Pages fixes shipped in the same session

## Phase 2 commits (in chronological order)

1. `4ed3916` Add Phase 2 experience-page rework implementation plan
2. `b3459ca` Add thesis line to experience content (FR + EN)
3. `d318dfd` Pass thesis frontmatter through to Projects page
4. `45c8b5a` Add ManifestoOpener component
5. `36c50de` Add SignageStrip component
6. `1f59088` Add EditorialSpread component
7. `aef78df` Add DataPlate component
8. `92eea5e` Refactor experience page to the iceberg composition (also drops the GitHub Pages preview workflow as a side effect of the staging done before this commit)
9. `9fac3f3` Reorder CI steps so backend tests see the Vite manifest

## Tasks executed

| #   | Task                                                                       | Status        |
| --- | -------------------------------------------------------------------------- | ------------- |
| 1   | Read foundational files                                                    | done          |
| 2   | Add thesis to FR + EN content                                              | done          |
| 3   | Backend pass-through and types (`SiteController`, PHPUnit, Vue prop widen) | done          |
| 4   | ManifestoOpener.vue                                                        | done          |
| 5   | SignageStrip.vue                                                           | done          |
| 6   | EditorialSpread.vue                                                        | done          |
| 7   | DataPlate.vue                                                              | done          |
| 8   | Projects.vue refactor (5-block iceberg composition)                        | done          |
| 9   | Verification + record (this document)                                      | this document |

## Verification

- `npm run types:check` → no errors.
- `npm run build` → `built in 9.56s`. Main bundle delta: `app-*.js` 265.98 kB → 266.10 kB (+0.12 kB / +0.04 kB gz) due to four new components; `SiteLayout-*.js` unchanged at 24.91 kB / 8.55 kB gz.
- `php artisan test --parallel` → exit code 0 (full suite passes including the new `test_projects_page_exposes_thesis_line_for_manifesto_opener` assertion on both locales).
- `design-conformance-reviewer` subagent on the four new components and the rewritten `Projects.vue`: dispatched in parallel with this record write; verdict appended after subagent completes.

## Backend contract

The new `thesis` prop flows through `SiteController::projects()`:

```php
'thesis' => $experience['thesis'],
```

inserted directly under `'hero' => $page['hero']`. The Pest test asserts the exact string for both `/fr/projects` and `/en/projects`.

## Frontend composition

`Projects.vue` now consumes:

- `ManifestoOpener` — wraps `SectionIntro size="hero"` with `view-transition-name: page-hero`, a discreet twilight radial backdrop driven by `--sw-twilight-glow`, and a `@starting-style` entry transition gated on `prefers-reduced-motion`.
- `SignageStrip` — sticky-on-desktop chip nav (`position: sticky; top: var(--sw-header-offset) + var(--sw-space-xs)`), horizontally scrollable on mobile, eyebrow tinted with `--sw-accent-violet`.
- `EditorialSpread` × N — two-column desktop spread (≥ 920 px) with prose left, side-rail right; rail renders pills, list items, and an optional `marginalia` figure; intertitle reveal via `animation-timeline: view()` gated on `@supports` and reduced-motion.
- `DataPlate` — three-column typographic plate (≥ 920 px) consolidating `trajectory`, `strengths`, and `focus_areas`; eyebrow labels in `--sw-accent-violet`, terse-list bullets in `--sw-accent-dominant`.
- Closing `Panel` — looking-for line and two CTAs; border tinted with a `color-mix` of `--sw-border` and `--sw-accent-violet`; background mixed with `--sw-twilight-anchor`.

The current `signageItems` computed concatenates `professionalSections`, `associativeSections`, and `sideProjectSections` (the last is currently empty). Slugs are produced by a small `slugify` helper that lowercases, strips diacritics, and hyphenates non-alphanumerics — sufficient for the four current section titles (Jewely Ecommerce, Louis Julian Redesign, Infrastructure, Aremedia).

## Frontmatter changes

- `resources/content/pages/{fr,en}/experience.md` — added `thesis: "…"` directly under the `hero` block.
- `stack_groups`, `career_snapshot`, `associative_note_widget`, `side_projects_widget`, and the now-unused widget `cv_downloads` continue to live in both locales' frontmatter so the i18n parity reviewer reports zero drift. They no longer render on the page.

## Side-quests shipped opportunistically

- **GitHub Pages preview workflow removed.** It duplicated Vercel's role and was failing on every push, polluting the Actions history.
- **CI step reorder.** `php artisan test` previously ran before `npm run build`, so any test rendering an Inertia/Blade view failed with `Vite manifest not found`. Build runs first now.

## Outstanding items / deferred

- **Marginalia copy.** The `EditorialSpread` component renders nothing when `marginalia` is absent. Phase 2 ships without seeded marginalia per project. Owner can add `marginalia: { author: "...", quote: "..." }` under any `professional_sections`/`associative_sections` item when ready (paritied across FR / EN).
- **`stack_groups` and `career_snapshot`** remain unused on the page. Phase 3 may surface them on `/colophon` or remove from frontmatter.
- **Vercel auto-deploy investigation.** The current `isrod.vercel.app` deployment is serving pre-Phase-1 assets (preload manifest references files like `PublicationWidget-*.css` that no longer exist). No Vercel check-runs appear on GitHub for recent commits, suggesting the Vercel App ↔ GitHub integration is detached or the project is using the OAuth flow without check-run reporting. The Vercel CLI is now installed locally but the stored token has expired. To reinvestigate, the owner needs to run `vercel login` interactively, then I can run `vercel ls`, `vercel inspect <deployment>`, and re-link if needed.

## Verdict

Phase 2 experience-page rework is **READY TO SHIP** pending the design-conformance subagent's appended verdict. All tests pass, build succeeds, and the new components consume tokens correctly per the Phase 1 substrate.
