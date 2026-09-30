# Site Refresh — the site plan: Implementation Plan

> **For agentic workers:** executed natively, in this order, one commit per task. Broad strokes on purpose: the owner asked for a short plan and for the biggest pieces to be built first, with **tests and quality gates run once, at the end** (Task 9), not after each step.

**Goal:** Redraw the public site as a site plan: flat, paper and ink, mirrored light and dark themes, a persistent background Stage, animated schemas, and a Work page.

**Architecture:** Keep the theme identifiers `morning` (now the paper theme) and `sunset` (now the plain night theme) so that no type, test or loader quote is renamed; rewrite what they *mean* in `tokens.css`. The background is one persistent Vue component (`Stage.vue`) mounted in `SiteLayout.vue`, outside the Inertia page swap. Diagrams are one data-driven SVG component (`Schema.vue`) fed by declared page content. The Work page follows the existing declared-content pipeline.

**Tech Stack:** Laravel 13, Inertia 3, Vue 3, TypeScript, Vite 8, hand-written CSS on `--sw-*` tokens, Switzer + DM Mono (self-hosted woff2).

**Spec:** `specs/018-site-refresh/spec.md`

## Global Constraints

- Constitution principles 1–9; Principle 9 is amended before any visual change (Task 1).
- Code, docs and keys in English; public copy FR and EN in strict shape parity.
- Tokens only (`--sw-*`): no hard-coded colour, font, spacing or motion in a component.
- `backdrop-filter`: 0 occurrences. Decorative `gradient(`: 0 occurrences.
- Motion: `transform`, `opacity`, `stroke-dashoffset`, motion paths only; paused when hidden or out of view; still under `prefers-reduced-motion`.
- Background JavaScript ≤ 25 KB; canvas DPR capped at 2; fewer particles on small screens.
- Primaries: yellow = marker, blue = link/dimension/path, red = "you are here"/attention; never two in one screen area.
- Accessibility: 4.5:1 text contrast in both themes; every schema has a text equivalent.
- Consent Mode v2 **basic** for GA4: no `gtag.js` and no request before consent.

## Review Focus

- A schema with one node, no edges, or a label twice as long in French as in English must still draw and read.
- Navigating fast (ten links in a row) must not restart or stack the Stage.
- A tab hidden for an hour, then shown, must not burst the particles or stains.
- `prefers-reduced-motion` toggled while the page is open must stop and start the motion.
- A visitor with storage blocked must still get a theme and no script error.
- A 320 px screen and a 200 % text size must not overflow horizontally.

## File structure

| Path | Responsibility |
|---|---|
| `.specify/memory/constitution.md`, `CLAUDE.md`, `AGENTS.md`, `CONTRIBUTING.md`, `docs/style/*` | The amended contract and style docs (Task 1, 9) |
| `resources/css/tokens.css` | Paper, night, greys, primaries and their roles (Task 2) |
| `resources/css/*.css`, `resources/js/**/*.vue` | The sweep: no blur, no decorative gradient (Task 2) |
| `resources/css/fonts-deferred.css`, `typography.css`, `public/fonts/*` | Switzer + DM Mono (Task 3) |
| `resources/js/components/stage/Stage.vue`, `useStage.ts`, `stains.ts` | Persistent background (Task 4) |
| `resources/js/components/schema/Schema.vue`, `schema.ts` | Data-driven animated diagram (Task 5) |
| `resources/js/pages/Home.vue`, `resources/content/pages/{fr,en}/home.md` | The home plan (Task 6) |
| `resources/js/pages/Work.vue`, `resources/content/pages/{fr,en}/work.md`, `app/Content/Schema/PageSchemas.php`, routes, controller | Work page (Task 7) |
| `resources/js/components/layout/AppHeader.vue`, `NavTabs.vue`, `AppFooter.vue`, `usePageTransitions.ts`, `view-transitions.css` | Header, footer, plan-zoom transition (Task 7) |
| `app/Seo/*`, `resources/js/lib/consent*.ts`, `config/consent.php`, `docs/rgpd/*` | JSON-LD, GA4 provider (Task 8) |

## Tasks

### Task 1: Amend the contract
Rewrite Principle 9 of `.specify/memory/constitution.md` (keep restraint and function; replace the atmosphere with the site-plan language); update `CLAUDE.md` (two themes: `morning` paper / `sunset` night, mirrored; drop "no green and no amber"), `AGENTS.md`, `CONTRIBUTING.md`. Commit.

### Task 2: Tokens and the sweep
Rewrite `tokens.css`: paper (`#F5F4F0` / ink `#121212` / five greys) and night (`#0E0E0F` / `#EDEDEA`), the three primaries with role tokens (`--sw-mark`, `--sw-path`, `--sw-here`), radius 0–2 px, motion tokens. Remove glass and gradient tokens. Sweep every `backdrop-filter` and decorative `gradient(` in `resources/css` and `resources/js` (replace with flat token fills and hairline borders). Retune the consent banner variables in `app.css`. Commit.

### Task 3: Typography
Self-host Switzer (variable woff2) and use DM Mono (already a dependency) in `fonts-deferred.css`; set `--sw-font-*` (text and titles: Switzer; labels, legends, dimension text: DM Mono capitals); remove Fraunces, Syne and DM Sans imports and dependencies; add licences to the colophon content. Commit.

### Task 4: The Stage
`Stage.vue` mounted once in `SiteLayout.vue`: a canvas of particles (clear on night, ink on paper; pointer and tilt parallax; a shift and a twinkle on each Inertia navigation; paused on `visibilitychange` and when reduced motion is on) and 6–8 flat SVG stains (`mix-blend-mode: multiply`, `transform`-only drift, 60–120 s, seeded per session, about 70 % grey and 30 % primary at low opacity). Commit.

### Task 5: The Schema component
`Schema.vue` + `schema.ts` (types: nodes, edges, dimension lines, compass): SVG drawing, `stroke-dashoffset` flow on edges, `offset-path` travelling markers, scroll-driven rotation of compass and arrowheads, `aria-hidden` drawing with an ordered-list text equivalent, still fallback. Commit.

### Task 6: The home plan
Redo `Home.vue` and `home.md` (FR/EN) as the site plan: lots, interfaces, graphic scale, north arrow, legend, revision index; on mobile a vertical strip of lots with a sticky mini-plan. Commit.

### Task 7: Work page, navigation, transitions
`work.md` (FR/EN), `PageSchemas.php`, route, controller, `Work.vue` (four public lots, a phase schedule, the revision index); header and footer links; plan-zoom View Transition with a fade fallback; sitemap entry. Commit.

### Task 8: SEO and measurement
`Person` JSON-LD: `sameAs` (GitHub, astralmanach.eu, uavv.fr), locality-only `address`, Work page structured data; GA4 provider in the consent registry (Consent Mode v2 basic, `G-KYTKR38YPX`) and `docs/rgpd`. Commit.

### Task 9: Gates and docs
Update `docs/style/*`, `docs/rgpd/*`, `docs/seo/*`; then, once, run `npm run check`, `composer run lint:check`, `php artisan test`, `npm run build` and the SSR build, fix what fails, run the parity tests, check the 360 px layout and Lighthouse accessibility on both themes. Commit.
