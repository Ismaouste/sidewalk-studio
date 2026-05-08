---
date: 2026-05-08
status: proposed
audience: design-dev
---

# Sidewalk Studio Iceberg Rework — Design

## Context

Sidewalk Studio is a personal portfolio for Isma — a tech lead targeting CTO positions, with an explicitly atypical profile combining engineering depth and cultural / creative sensibility. The site is built on Laravel 12 + Inertia + Vue 3 + Tailwind v4 + Vite, deployed to Vercel, with two named themes: `morning` (Morning Grid, light, architectural) and `sunset` (Sunset Signal, dark, cinematic).

Three observations trigger this rework:

1. **The experience page rendering is linear and reads flat.** A waterfall of similar story panels does not match the editorial / wayfinding ambition declared in `docs/style/art-direction.md`.
2. **Strong personality signals already exist on the site but are under-orchestrated.** The journal articles (notably _Les interdits comme spécification_), theme naming, loader quotes, build journal mirrors, constitution, privacy-first contact form, and Spec Kit discipline collectively communicate an atypical CTO-grade profile, but visitors rarely encounter all of them.
3. **The technical foundation is sound but partly outdated.** `AmbientGrid` is a runtime-JS atmospheric component flagged HIGH on both perf and design audits. Modern web platform features (View Transitions, scroll-driven animations, `@property`, OKLCH, popover) can replace JS-driven motion with CSS-only equivalents while elevating craft.

The owner's brief: _"design discret mais ambitieux state of the art cutting edge technologies micro animations partout"_. The intended posture is an **iceberg portfolio**: the visible surface stays calm; the substance reveals itself to those who pay attention.

## Goals

- **Ship a measurably better experience page** that orchestrates the existing dense content into a layered narrative serving both a 30-second recruiter scan and a 5-minute deep read.
- **Replace runtime-JS atmosphere with CSS-first equivalents** (AmbientGrid in particular), addressing perf-audit HIGH findings and freeing JS budget for genuine interactivity.
- **Elevate existing personality vectors** — theme naming, loader quotes, journal categorization, public build journal, articles like _Les interdits comme spécification_ — so visitors encounter them in passing rather than by hunting.
- **Introduce six modern web techniques** in the substrate: `@property`, scroll-driven animations, View Transitions, `@starting-style`, OKLCH + `color-mix()`, Popover API + anchor positioning. Plus zero-cost garnish: `text-wrap: balance`, `<details name>`, Speculation Rules `prefetch`.
- **Preserve FR/EN frontmatter parity** (currently 100% per audit) and `morning` / `sunset` theme parity for every public component.

## Non-goals

- No new design language. The existing `docs/style/*` vocabulary stands.
- No JavaScript animation libraries (GSAP, Anime, Motion One, etc.). CSS-first.
- No re-platform. Laravel + Inertia + Vue 3 + Tailwind v4 + Vite stays.
- No IE / pre-2023 browser parity. Modern features ship as progressive enhancement; non-critical effects can be absent in trailing browsers.
- No breaking of existing routes or content slugs. URLs stay backwards-compatible.
- No running the full test suite or `vue-tsc` after every edit (per `CLAUDE.md`); validate at logical phase checkpoints only.

## Architecture overview

The rework lands in three phases, executed in order. Phase 1 lays the substrate that Phases 2 and 3 consume. Phase 2 is the headline (experience page). Phase 3 polishes the surrounding shell.

### Tech package

| Capability                                      | Use                                                      | Phase | Browser baseline (early 2026)                                                    |
| ----------------------------------------------- | -------------------------------------------------------- | ----- | -------------------------------------------------------------------------------- |
| `@property` typed CSS variables                 | Animate `--sw-*` tokens directly in CSS                  | 1     | Newly available 2024                                                             |
| Scroll-driven animations (`animation-timeline`) | Replace `MutationObserver` and JS scroll polling         | 1     | Limited (Chrome / Safari); Firefox flagged — progressive enhancement             |
| View Transitions (same-document)                | Inertia navigation crossfade + named-element morph       | 1     | Newly available, Firefox 144 (Oct 2025)                                          |
| `@starting-style`                               | Loader / popover / accordion entry                       | 1 / 3 | Newly available 2024                                                             |
| OKLCH + `color-mix()`                           | Token palette refactor for perceptually uniform variants | 1     | Widely available                                                                 |
| Popover API + anchor positioning                | Locale switcher, share menus                             | 3     | Popover widely available; anchor in Firefox 147 (Jan 2026), `@supports` fallback |
| `text-wrap: balance`                            | Site-wide title balancing                                | 1     | Widely available (`pretty` is Limited)                                           |
| `<details name>`                                | Native exclusive accordion                               | 3     | Newly available 2025                                                             |
| Speculation Rules (`prefetch` only)             | Internal link prefetch on Chromium                       | 1     | Limited; silent fallback elsewhere                                               |

Explicitly rejected: `interpolate-size` / `calc-size` (Chromium-only), Houdini Paint API (no Firefox), `text-box-trim` (limited), cross-document View Transitions (no Firefox), variable-font animation as a flagship technique.

### Phase boundaries

- **Phase 1 — Substrate**: token refactor, AmbientGrid replacement, View Transitions plumbing, prefetch rules, font deferral. All public pages benefit. No new content surfaces.
- **Phase 2 — Experience page rework**: new page composition consuming Phase 1 substrate. Hero with thesis + signage chips, editorial spreads with side-rail data, bookends. Optional marginalia for the cultural anchors that fit each project (Gary on Infrastructure, Cannes-1862 typography on Louis Julian, etc.).
- **Phase 3 — Journal & navigation polish**: optional third content type `dossier`, `/colophon` discoverable route, loader-quote elevation, FR/EN switcher reframe via Popover, single sparkle-ethos breath into the shell.

## Phase 1 — Substrate

### Token refactor (OKLCH + `@property`)

**Files modified**: `resources/css/tokens.css`, `resources/css/reset.css` (only if cascade requires).

- Express the `morning` and `sunset` palettes in OKLCH at the source. Keep the public surface (`--sw-bg-*`, `--sw-text-*`, `--sw-accent-*`) as-is so consumer code does not change.
- Derive hover, focus, and border tokens via `color-mix(in oklch, ...)` instead of hand-tuned hex.
- Register animatable tokens with `@property`:
    - `--sw-sun-angle: <angle>; initial-value: 8deg`
    - `--sw-grid-line-opacity: <number>; initial-value: 0.4`
    - `--sw-ambient-flare-1..4: <color>` per theme
- Introduce a small set of `--sw-twilight-*` interpolation tokens that mix Morning and Sunset endpoints — used by the experience page hero gradient.

### AmbientGrid replacement (CSS-only)

**Files modified**: `resources/js/components/design-system/AmbientGrid.vue` — full rewrite.

- Drop the JS palette arrays entirely.
- Token-driven palette read via `var(--sw-ambient-flare-*)`.
- Movement via `@keyframes` driven by `@property`-typed tokens, animated by a single `animation-timeline: scroll(root)` declaration, gated by `@supports (animation-timeline: view())` and `@media (prefers-reduced-motion: no-preference)`.
- No `MutationObserver`. Theme switching changes `html[data-theme]`; CSS picks up new token values automatically.
- Remove `will-change` from elements no longer requiring compositor promotion.

### View Transitions on Inertia navigation

**Files modified**: `resources/js/app.ts`, new `resources/css/view-transitions.css`.

- Wrap Inertia's navigation in `document.startViewTransition` when supported. Skip when `prefers-reduced-motion: reduce`.
- Define named transitions only on a small set of high-signal elements:
    - Page hero: `view-transition-name: page-hero`
    - Article cover image (Writing/Show): `view-transition-name: article-cover-{slug}`
- CSS for transitions in `view-transitions.css`, gated on `@supports (view-transition-name: none)`.

### `@starting-style` adoption

**Files modified**: existing loader component (locate in `resources/js/lib/staticPreview.ts` and any consent loader), `resources/js/components/ui/Panel.vue` for entry reveals.

- Replace existing Vue `<Transition>` wrappers around the loader and reveal blocks with `@starting-style` declarations. Keep `<Transition>` only where Vue mount/unmount lifecycle is genuinely needed.

### Prefetch + font deferral

**Files modified**: `resources/css/app.css` (or new `resources/css/fonts.css`), `resources/js/app.ts`, a Blade layout (likely `resources/views/app.blade.php`) for `<script type="speculationrules">`.

- **Fonts**: import only DM Sans 400 / 500 critical. Defer Fraunces (display), Syne (label / nav), DM Mono (code) via `font-display: swap` and conditional CSS load on pages that need them.
- **Speculation Rules**: emit a `<script type="speculationrules">` from a Blade layout with `prefetch` rules, eagerness `moderate`, scoped to internal links. Not `prerender` (would break `vanilla-cookieconsent` analytics gating).
- **Static prefetch handlers** (`resources/js/lib/staticPreview.ts`): debounce pointer / touch listeners; skip on `connection.saveData` or 2G / 3G.

### Phase 1 verification

- Bundle report (`npm run audit:bundle`) shows reduced font footprint and AmbientGrid no longer in JS surface.
- Lighthouse (`npm run audit:lighthouse` + `:mobile`) — perf score must not regress; FCP should improve.
- Manual visual diff on both themes for Home, current Experience (`/experience`), Writing index / show, CaseStudies index, Contact.
- Subagent pass: `design-conformance-reviewer` on changed Vue / CSS files.

## Phase 2 — Experience page rework

### Page composition

Replace the current `Projects.vue` rendering for `/experience` with a five-block composition:

1. **Manifesto opener** — one-sentence thesis in display Fraunces with `text-wrap: balance`. Eyebrow "Comment je travaille" / "How I work". Three-line summary. View-transition-named hero block.
2. **Signage strip** — horizontal row of four contextual chips (Jewely, Louis Julian, Infrastructure, Aremedia), each carrying year + a one-line role. Anchored as the page's wayfinding spine. Each chip jumps to the corresponding spread when activated.
3. **Editorial spreads** — one per professional / associative section. Two-column desktop layout (paragraphs on the left, side-rail on the right with stack pills, year, role). On mobile the side-rail stacks below. Scroll-driven `view-timeline` reveals intertitles as they enter the viewport.
    - Optional **marginalia** in side-rail: a 1-line italic citation aligned with the project's cultural register.
4. **Trajectory & strengths** — compact data plate consuming `trajectory`, `strengths`, `focus_areas` from the existing frontmatter. Treated as a single editorial typography unit, not three separate panels.
5. **Looking-for closer** — single restrained panel with `looking_for` copy and two CTAs (case studies + contact).

The current top "3 Panel grid" (Positioning / Contexts / Career Snapshot) is dissolved:

- `positioning` → manifesto opener
- `contexts` → signage strip
- `careerSnapshot` → trajectory & strengths data plate

### Components to create

- **`<ManifestoOpener>`** — `resources/js/components/experience/ManifestoOpener.vue`. Props: `eyebrow`, `thesis`, `summary`. Uses `SectionIntro` primitive, adds `text-wrap: balance` and `view-transition-name: page-hero`.
- **`<SignageStrip>`** — `resources/js/components/experience/SignageStrip.vue`. Props: `items: Array<{ id, eyebrow, label, href }>`. Renders a row of `LegendChip`s that scroll-jump to anchors on click. Sticky on desktop scroll within the experience-page section; non-sticky on mobile.
- **`<EditorialSpread>`** — `resources/js/components/experience/EditorialSpread.vue`. Props: `eyebrow`, `title`, `paragraphs`, `pills`, `items`, `years`, `marginalia?`. Two-column layout, scroll-timeline for reveal.
- **`<DataPlate>`** — `resources/js/components/experience/DataPlate.vue`. Props: `trajectory`, `strengths`, `focusAreas`. Single typographic block; `--sw-font-display` for headers, `--sw-font-body` for items.

### Frontmatter additions (FR + EN, parity-safe)

Add to `resources/content/pages/{fr,en}/experience.md`:

```yaml
thesis:
    fr: "Reprendre l'existant, le rendre lisible, et le laisser plus calme qu'à l'arrivée."
    en: "Take over what's there, make it readable, and leave it calmer than I found it."

# under each professional_sections / associative_sections item, optional:
marginalia:
    author: 'Romain Gary'
    quote: '...'
```

The `marginalia` key is **optional** — components render nothing when absent. The `i18n-parity-reviewer` subagent enforces presence in both locales when used.

Marginalia candidate authors per project (proposed; final copy approved by owner before ship):

- **Jewely** — heritage-leaning, restrained
- **Louis Julian** — Cannes-1862 typographic eyebrow
- **Infrastructure** — Romain Gary on dignity-as-humour or systems-as-ethics
- **Aremedia** — civic / privacy-leaning anchor

### Data flow

Backend `SiteController::experience()` already exposes the rendered frontmatter. No controller change needed for the new keys — Inertia passes them through. If `marginalia` is absent, the component renders nothing; no error.

### Motion specifics

- Manifesto opener fades in via `@starting-style` on first paint.
- Signage strip chips have a hover micro-tilt (`transform: translateY(-1px)`), gated on `prefers-reduced-motion`.
- Editorial spread intertitles reveal via `animation-timeline: view()` with a 30 % scroll offset.
- View transition on navigation morphs the `page-hero` named element.
- All gates respect `@media (prefers-reduced-motion: reduce)` — animations collapse to instant.

### Theme behavior

Both themes must read distinctly. The hero gradient uses the `--sw-twilight-*` interpolation tokens. Sunset turns the manifesto eyebrow into a warm glow (`color-mix` with `--sw-accent-coral`); morning keeps it crisp.

### Phase 2 verification

- `design-conformance-reviewer` on all new and modified Vue / CSS files.
- `i18n-parity-reviewer` on `experience.md` after frontmatter additions.
- Lighthouse on `/experience` — score must not regress vs Phase 1 baseline.
- Manual side-by-side morning / sunset comparison on the four spreads.

## Phase 3 — Journal & navigation polish

### Optional `dossier` content type

Add a third `category` value `dossier` to the Writing content schema:

- `category: dossier`, `publication_type: dossier` on relevant entries.
- New chip tone for dossier (proposal: `sun`, since `journal=violet`, `note=coral`).
- Dossiers list multiple sub-articles via a frontmatter field `chapters: [{ title, slug }]`.

This is **opt-in**: if the owner does not yet have a curated dossier, ship Phase 3 without dossier-specific UI and add it later. The schema work itself (frontmatter shape, controller deserialization, chip tone) is preparatory; the first published dossier is a content task, not a code task.

### `/colophon` discoverable route

A new route `/{locale}/colophon`, linked from the footer in small text. The page surfaces:

- The constitution principles (excerpts from `.specify/memory/constitution.md`)
- The privacy stance (link to `/data-processing`)
- The build journal entries (links to public ones)
- The stack and hosting choice
- A short "this site as public infrastructure" paragraph

Tone: matter-of-fact, not promotional. Single column, restrained. Same theme parity rules as the rest.

**Files**: new `resources/content/pages/{fr,en}/colophon.md`, new `resources/js/pages/Colophon.vue`, new route in `routes/web.php`, new method on `SiteController`.

### Loader-quote elevation

- Persist a small "quote of the load" indicator in the footer area: the most recently displayed quote, rendered as a discreet colophon line.
- Use View Transitions on the loader to fade between quote and target page rather than a hard cut.
- Confirm theme-targeting still works (already supported by the admin UI).

### FR/EN switcher reframe via Popover

- Rebuild `LocaleSwitcher.vue` with the Popover API: `<button popovertarget>` + `[popover]` + anchor positioning (`@supports` fallback to `position: absolute`).
- Add a one-line eyebrow inside the popover acknowledging the tonal difference between FR and EN ("Le ton change un peu d'une langue à l'autre" / "The tone shifts a little between languages").
- Verify keyboard / focus management: native popover gives this for free if used correctly.

### Sparkle ethos breath

A single, contextual escape from the hidden Sparkle page into the main shell. Pick **one** option (not both):

- The footer copyright line gets a discreet decorative em-dash with a subtle `font-feature-settings` ligature on hover.
- The Home hero opener gets one `font-variation-settings` micro-shift on hover that hints at the Sparkle ethos without naming it.

### Phase 3 verification

- `design-conformance-reviewer` on all new components and rewritten `LocaleSwitcher.vue`.
- `i18n-parity-reviewer` on `colophon.md` once written.
- Backend test: if a new route lands, `php artisan test` runs once at phase end (per `CLAUDE.md`).
- Manual a11y check: keyboard navigation through the popover, dossier nav (if shipped), colophon page.

## Cross-cutting concerns

### i18n parity

Every new frontmatter key lands in both `fr/` and `en/`. The `i18n-parity-reviewer` subagent runs after content edits.

### Performance budget

- No new npm dependencies that affect the runtime bundle.
- Net JS bundle should decrease by Phase 1 end (AmbientGrid simplification + removal of any floating-ui-style runtime in Phase 3 popovers).
- LCP on `/experience` must not regress vs current; target a small improvement.

### Theme parity

Every new component renders distinctly across `morning` and `sunset`. The `design-conformance-reviewer` subagent checks for hardcoded values and theme-blind color usage.

### Motion guardrails

All keyframes and transitions over 200 ms gate on `@media (prefers-reduced-motion: no-preference)`. No parallax, no autoplay decorative surfaces, no page-specific animation systems beyond the substrate-defined patterns.

### Accessibility

- Focus management on Popover, anchor positioning, View Transitions — verify with keyboard-only navigation.
- Reading-progress on article pages (Phase 1 substrate, optional polish in Phase 3) provides visual cue but does not replace WAI-ARIA progress where appropriate.
- All new color tokens meet WCAG AA contrast in both themes.

### Browser support stance

- Chrome / Edge / Safari: all six substrate techniques work.
- Firefox: scroll-driven animations remain behind a flag in early 2026 — provide static end-state fallback. Anchor positioning lands in Firefox 147 — `@supports` fallback for older Firefox.
- Older browsers: substrate techniques degrade silently (no visual breakage), thanks to `@supports` and progressive enhancement.

## Files touched (preview)

### Phase 1

- `resources/css/tokens.css` (refactor)
- `resources/css/app.css` (font deferral)
- `resources/css/view-transitions.css` (new)
- `resources/js/app.ts` (View Transitions plumbing, prefetch debounce)
- `resources/js/lib/staticPreview.ts` (debounce, connection-aware skip)
- `resources/js/components/design-system/AmbientGrid.vue` (full CSS-only rewrite)
- `resources/views/app.blade.php` (`<script type="speculationrules">`)

### Phase 2

- `resources/content/pages/{fr,en}/experience.md` (new keys: `thesis`, optional `marginalia` per section)
- `resources/js/pages/Projects.vue` (full rewrite of template; Vue file path unchanged for backwards compatibility with the Inertia route)
- `resources/js/components/experience/ManifestoOpener.vue` (new)
- `resources/js/components/experience/SignageStrip.vue` (new)
- `resources/js/components/experience/EditorialSpread.vue` (new)
- `resources/js/components/experience/DataPlate.vue` (new)

### Phase 3

- `resources/content/pages/{fr,en}/colophon.md` (new)
- `resources/js/pages/Colophon.vue` (new)
- `routes/web.php` (new route)
- `app/Http/Controllers/SiteController.php` (new method)
- `resources/js/components/layout/LocaleSwitcher.vue` (rewrite using Popover API)
- `resources/js/components/layout/AppFooter.vue` (loader-quote colophon line, sparkle breath option)
- (optional) Writing schema docs + `Index.vue` + `Show.vue` chip-tone update for dossier

## Open questions

1. **Dossier content type — green light?** This spec keeps it scoped as opt-in for Phase 3 (schema + UI shell, no curated dossier shipped yet).
2. **Loader-quote sourcing** — are there enough seeded quotes per locale × theme to make the footer colophon line non-repeating across one session? If not, Phase 3 includes a small content step to top up.
3. **Marginalia copy** — the spec lists candidate registers per project but does not commit specific quotes. Owner approves final marginalia before Phase 2 ships.
4. **`prefers-reduced-motion` interaction with View Transitions** — modern browsers honor it for crossfades; verify on Safari and Firefox during Phase 1 verification.

## Out of scope

- A new design language or token system beyond the Phase 1 refactor.
- React Native / mobile app.
- Backend rewrites (Laravel + SQLite stay).
- New deployment targets (Vercel stays).
- A new CMS layer (markdown + frontmatter stays).
- The Sparkle page rewrite — keep as-is; only let one breath escape into the main shell.
- Image asset overhaul — existing media remains; only `loading="lazy"` + `decoding="async"` added in Phase 1.
- Translation of new copy into languages other than FR / EN.

---

_This design will be expanded into a step-by-step implementation plan via the `superpowers:writing-plans` skill._
