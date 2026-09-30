---
linear_issue: TODO
github_project_item: TODO
github_project_status: proposed
obsidian_note: TODO
release: TODO
title: Site Refresh — the site plan
status: proposed
---

# Feature Specification: Site Refresh — the site plan

The public site is redrawn as a **site plan** (a *plan de chantier*): the owner
is a multi-skilled project lead who coordinates trades, and the site shows how
he works. Sober in colour, light and dark in mirror, with a slow moving
background and many diagrams whose arrows turn, mobile first.

## Problem

Three problems, measured on the current site rather than felt.

**The look is glossy, and the owner does not like it.** The interface is built
out of frosted glass and gradients: `backdrop-filter` appears 40 times and
`gradient(` 75 times across `resources/css` and `resources/js`. The light
theme (`morning`) sits on a peach wash; the dark theme (`sunset`, "violet
glass") is the gamer-dark look the owner wants gone. Four saturated accents
(orange `#e6722e`, coral `#d83a2a`, green `#2a8754`, blue `#1f5fd6`) compete on
the same screen. The radius scale is already capped at 6px, so the softness
comes from blur, gradient and colour, not from rounding.

**The site says nothing about the work that can be checked.** A visitor
finds a current role, a services page and articles, but not the things that
exist in public and that a hiring manager can open in one click: a published
npm library (MIT, compiled ESM and types, provenance), a public read-only API
with an OpenAPI description and a Postman collection, three working tools built
with that library, the website of an art project (Un art voulu voyant) and the
portfolio of its author (Florian Rosinski), both developed by the owner, and a
services website for Atlas Dépannage. None of them is named in
`resources/content`.

**The identity does not say what the owner is.** His profile is transversal:
engineering depth, openness to open data, a working relationship with the
artistic milieu and a real reflection on scenography. Nothing in the current
visual language says "someone who coordinates several trades".

## Desired outcome

**The metaphor is the site plan.** A project lead coordinating trades on a
worksite reads plans, sections, dimension lines, phases and revision indices.
The site is drawn the same way:

- The **home page is a plan**: a site plan on which each public project and each
  service is a **lot** (a work package). Arrows between lots are the
  **interfaces between trades** — the coordination is the content. A graphic
  scale, a north arrow and a legend are part of the drawing.
- A **Work page** lists the public lots: astralmanach (library, API, three
  tools), Un art voulu voyant (uavv.fr), florianrosinski.fr, Atlas Dépannage. A
  small **schedule** (a bar chart of phases) says what was delivered and when;
  each page carries a **revision index** (A, B, C…) like an execution drawing.
- **Sober colour.** Paper and ink; dark grey to black. The three primaries are
  the colours of a worksite drawing and carry meaning, never decoration:
  safety **yellow** for a marker or a highlight, plan **blue** for links,
  dimension lines and paths, **red** for "you are here" and for what needs
  attention. Never more than one primary in a given area of the screen.
- **A light and a dark theme, in mirror.** The same system of particles is
  clear points on black at night and ink points on paper by day, with the same
  motion and the same drift at every page change (the effect of uavv's dark mode,
  reused and mirrored).
- **A slow background of stains.** A few flat organic shapes drift slowly behind
  the page. They live in the persistent layout, so a fast navigation does not
  restart them.
- **Many diagrams, and they move.** Schemas with arrows, compass roses and
  dimension lines that turn, driven by scroll and by entry into view; richer
  animation is allowed, with a still fallback.
- **Mobile first.** Designed at 360 px, then enlarged.
- **Findable and contactable without a form**: where he is based (Nancy, Grand
  Est), his GitHub, `contact@isrod.eu`; audience measured through the consent
  orchestration already in place (GA4 `G-KYTKR38YPX`, Consent Mode v2 *basic*:
  nothing loads or is sent before consent).
- The constitution and the repository contract say what the site now is.

## In scope

- **Design tokens** (`resources/css/tokens.css`, `docs/style/tokens.md`,
  `docs/style/theme-system.md`): paper and ink scales for two mirrored themes,
  the three primaries with their roles, removal of the blur and gradient tokens,
  a motion scale that allows transform, opacity and stroke animation.
- **Typography**: the two-voice system shared with the sibling sites (Switzer for
  text and titles, DM Mono in capitals for labels, dimension text and legends),
  replacing Fraunces, Syne and DM Sans; self-hosted, deferred.
- **The Stage** (`resources/js/components/Stage.vue`, mounted in the persistent
  layout): one canvas of particles (clear on dark, ink on paper; pointer and tilt
  parallax; a shift and a twinkle at each route change) and 6 to 8 flat SVG
  stains (`mix-blend-mode: multiply`, no blur, no gradient) drifting by
  `transform` only, over 60 to 120 seconds, seeded per session, about 70 % grey
  and 30 % a primary at low opacity.
- **The Schema component** (`Schema.vue`): nodes, edges, dimension lines and
  compass roses declared as data in the page content (labels in FR and EN in
  strict parity), drawn in SVG, animated by `stroke-dashoffset`, `offset-path`
  and scroll-driven animations. Every schema has a text equivalent (an ordered
  list); the drawing is `aria-hidden`.
- **The home plan**: the site plan with lots, interfaces, scale, north arrow and
  legend; on mobile, a vertical strip of lots with a sticky mini-plan.
- **Page transitions**: View Transitions that zoom from the plan to the chosen
  lot (existing `view-transitions.css`), plain fade when the API is missing.
- **The Work page**: new public route, FR and EN in strict parity, declared in
  the content schema (`resources/content/pages/{fr,en}/work.md`); the four public
  lots, the schedule, the revision index. Every entry: what it is, what the
  owner did, one link.
- **Other pages**: each gets a header schema appropriate to its content (a flow
  for Services, an architecture diagram for a case study).
- **SEO**: `Person` JSON-LD with `sameAs` (GitHub, LinkedIn when set, astralmanach.eu) and
  a locality-only `address` (already present); the Work page in the sitemap. Un art voulu
  voyant belongs to Florian Rosinski and is not an identity of the owner, so it is not `sameAs`.
- **Measurement**: GA4 added to the consent orchestration as a provider, in
  Consent Mode v2 *basic* (no `gtag.js`, no request before consent); the decision
  against the existing first-party ping and PostHog is recorded (open question 6).
- **Documentation**: constitution (Principle 9), `CLAUDE.md`, `AGENTS.md` (the
  "two themes only" and "no green, no amber" rules), `CONTRIBUTING.md`,
  `docs/style/*`, `docs/rgpd/*`, `Roadmap.md`.

## Out of scope

- The back office (`/admin`): it keeps working with the new tokens.
- The articles and case studies: rendering changes, no rewriting. The owner's
  career documents (`docs/career/**`) are not touched.
- Advertising pixels, session replay, any analytics beyond GA4.
- Legal identity: the SIRET and a postal address join the legal pages when the
  micro-enterprise exists, in a separate change.
- Renaming the repository or the `--sw-*` token prefix.

## Constraints

- **Constitution, all nine principles** apply: spec before code, privacy by
  default, accessibility, SEO as architecture, clarity over cleverness,
  documentation with the feature, reuse, quality gates, art direction with
  restraint.
- **Principle 9 is amended first**, explicitly and with its reason: its text names
  "poetic dusk atmospheres" and "softness inside structure", which describe the
  look being removed. The amendment keeps "restraint" and "functional" and
  replaces the atmosphere with the site-plan language. Nothing visual ships
  before it is merged.
- **Motion** is richer than on the sibling sites, and bounded: `transform`,
  `opacity`, `stroke-dashoffset` and motion paths only (nothing that triggers
  layout or paint of large areas); paused when the tab is hidden or the element
  is out of view; everything stops, and diagrams render still, under
  `prefers-reduced-motion`.
- **The background** adds at most 25 KB of JavaScript, never blocks input, lives
  in the persistent layout, draws fewer particles on small screens, and caps the
  device pixel ratio at 2. No `backdrop-filter`, no blur filter, no decorative
  gradient anywhere.
- **Accessibility**: 4.5:1 for text and every interactive state in both themes;
  primaries used as fills never carry text without checking contrast; focus
  always visible; every diagram has a text equivalent; nothing flashes more
  than three times a second.
- Public copy in French and English in strict shape parity
  (`DeclaredPageContentTest`, `LanguageFileParityTest`); code, docs and keys in
  English. No CSS framework; tokens only; `@layer` and platform primitives
  preferred.
- Fonts self-hosted, subset, `font-display: swap`; Switzer (Indian Type Foundry)
  and DM Mono (OFL); their licences go in the colophon.
- Claims on the Work page must be verifiable from the link they carry; no client
  names, figures or testimonials the owner has not approved.
- Performance budget unchanged (Core Web Vitals, `npm run audit:lighthouse`).
- Quality gates before any push: `npm run check`, `composer run lint:check`,
  `php artisan test`, `npm run build`, and the SSR build.

## Acceptance criteria

- [ ] Principle 9, `CLAUDE.md`, `AGENTS.md` and `CONTRIBUTING.md` are amended and
      merged before any visual change.
- [ ] `backdrop-filter` and decorative `gradient(` have 0 occurrences in
      `resources/css` and `resources/js`.
- [ ] Light and dark themes are mirrors of one token set; the stored or system
      choice is applied before first paint (no flash); light is the default.
- [ ] The Stage persists across navigation (its particles and stains do not
      restart on a route change, measured), and stops under reduced motion.
- [ ] The three primaries appear only in their roles; never two in the same area
      of a screen.
- [ ] Every schema has a text equivalent; with animations off, each is readable
      as a still drawing.
- [ ] The Work page exists in `/fr` and `/en`, is linked from the header and the
      footer, is in the sitemap, and every entry links to its public address.
- [ ] Home, Work, Services, Contact and the legal pages score 100 in Lighthouse
      accessibility, mobile and desktop, in both themes; no horizontal scroll at
      360 px; tables scroll inside their own region.
- [ ] On a mid-range phone profile (4x CPU throttle), the home page keeps input
      responsive (INP under 200 ms) with the Stage running.
- [ ] `Person` JSON-LD validates with `sameAs` and a locality-only `address`.
- [ ] GA4 loads only after consent (no request to Google before, verified in the
      network panel); a returning visitor is counted once.
- [ ] `docs/style/*`, `docs/rgpd/*`, `docs/seo/*` and `Roadmap.md` describe the
      result; the parity tests pass.

## Decisions made

- **The specks are removed** (owner, 2026-09-30): the mirrored star field of the first
  version is gone; only the flat drifting stains remain in the Stage. The
  "mirrored light and dark" now concerns the two themes, not a particle system.
- **Tone**: public copy is plain and factual. The home, Work, Services, Contact,
  Projects, Local, Colophon, Data processing and Sparkle pages were rewritten
  without changing any fact; the articles and the loader quotes are not part of
  this pass.

- **Concept**: the site plan / the worksite (owner, 2026-09-30: "a multi-skilled
  project lead — the image of the project plan and the site foreman").
- **Both themes stay**, as mirrors of one token set; `sunset` and its violet glass
  are removed.
- **Colour**: paper, ink and greys, with the three primaries in their roles.
- **Motion**: rich and bounded as above.

## Open questions (owner's decisions)

1. **Typefaces.** Assumed Switzer and DM Mono, as on the sibling sites. The
   current display serif (Fraunces) carried the "cultured" tone; dropping it is
   the biggest visible change.
2. **The portrait.** Recommendation: out of the header; small on Contact.
3. **The name.** "Ismaël Rodmacq" everywhere (the current home page drops the
   diaeresis); URLs and identifiers stay ASCII.
4. **Wording of each lot**, especially Atlas Dépannage. Until written or
   approved, a name and a link only.
5. **The title.** "Project lead across trades" (*chef de projet transversal*), or
   "tech lead" as today, or both? It sets the first line a recruiter reads, and
   the current role (Jewely / Flippad) may keep its place or move down.
6. **GA4 next to the existing measurement design** (first-party ping, PostHog EU
   behind opt-in): add as a provider, or let GA4 replace PostHog. Needs a short
   decision record.
7. **The lots.** Which skills become lots of their own on the plan (for example
   e-commerce and product data, Laravel and APIs, technical SEO, privacy and
   consent, open data, art and scenography projects), and in what order.
8. **Order of work.** Recommendation: constitution amendment; tokens, fonts and
   the Stage; the Schema component and the home plan; the Work page and
   transitions; schemas on the other pages; SEO, consent and GA4; documentation.
   Each a separate, reviewable change.

## Tracking

- Linear: keep the primary issue key in `linear_issue:`
- GitHub Project: mirror `github_project_item`, `github_project_status`, and `release` in `docs/ai/github-project/roadmap-spec-issue-map.md`
- Obsidian: set `obsidian_note` to the repo mirror path under `docs/ai/obsidian/build-journal/`
- Codex execution: use the file-based workflow even if native `/speckit.*` commands are unavailable
