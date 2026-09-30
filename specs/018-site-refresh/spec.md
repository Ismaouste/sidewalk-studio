---
linear_issue: TODO
github_project_item: TODO
github_project_status: proposed
obsidian_note: TODO
release: TODO
title: Site Refresh
status: proposed
---

# Feature Specification: Site Refresh

The public site is redrawn as a plain, square, light-first object in the same
visual family as the owner's other sites (astralmanach.eu, uavv.fr), and it
starts showing the work that exists in public: the websites, the library and
the tools the owner has built for other people.

## Problem

Three problems, measured on the current site rather than felt.

**The look is glossy, and the owner does not like it.** The interface is built
out of frosted glass and gradients: `backdrop-filter` appears 40 times and
`gradient(` 75 times across `resources/css` and `resources/js`. The light
theme (`morning`) sits on a peach wash; the dark theme (`sunset`, "violet
glass") is the gamer-dark look the owner wants gone. Four saturated accents
(orange `#e6722e`, coral `#d83a2a`, green `#2a8754`, blue `#1f5fd6`) compete on
the same screen. The radius scale is already capped at 6px, so the softness
comes from blur, gradient and colour, not from rounding — which is also why
"making it less rounded" alone would change nothing.

**The site says nothing about the work that can be checked.** A visitor
looking for the owner finds a current role, a services page and articles, but
not the things that exist in public and that a hiring manager can open in one
click: a published npm library (MIT, compiled ESM and types, provenance), a
public read-only API with an OpenAPI description and a Postman collection, three
working tools built with that library, the website of an art project
(Un art voulu voyant) and the portfolio of its author (Florian Rosinski),
both developed by the owner, and a services website for Atlas Dépannage. None
of them is named anywhere in `resources/content`.

**The identity is split across three sites.** astralmanach.eu and uavv.fr are
monochrome, flat, square, light by default, set in Switzer and DM Mono, with
colour reserved for meaning. isrod.eu is the odd one out. Someone who follows a
link from one to the other should not feel they changed owner.

## Desired outcome

- The site reads as **ink on paper**: light by default, flat surfaces, hairline
  borders, square controls, one typographic system, one accent at most. No
  blur, no gradient as decoration, no glass.
- The same visual grammar as astralmanach.eu and uavv.fr: tokens, scale, the
  two voices (a proportional text face and a monospace capitals voice for
  labels, buttons and table heads).
- A **Work** page (and a short block on the home page) that names what exists
  in public, says what the owner did on each, and links to it. Claims are
  limited to what a link can confirm.
- The owner can be found and contacted without a form: the site states where
  he is based (Nancy, Grand Est), his GitHub, `contact@isrod.eu`, and
  measures audience through the consent orchestration already in place
  (`G-KYTKR38YPX`, Consent Mode v2, nothing before consent).
- The constitution and the repository contract say what the site now is, so
  that later work is judged against the new direction and not the old one.

## In scope

- **Design tokens** (`resources/css/tokens.css`, `docs/style/tokens.md`,
  `docs/style/theme-system.md`): a new neutral scale, one accent, removal of the
  blur and gradient tokens, a motion scale reduced to opacity changes.
- **Typography** (`typography.css`, `fonts-deferred.css`): the two-voice system
  shared with the sibling sites (Switzer for text and titles, DM Mono in capitals
  for labels), replacing Fraunces, Syne and DM Sans. Self-hosted, with the
  existing deferred-font strategy.
- **Components and layout**: every component that uses `backdrop-filter` or a
  decorative gradient is restyled as a flat surface with a hairline; navigation,
  cards, buttons, the consent banner and the footer first.
- **Themes**: `morning` becomes the paper theme; the dark theme is redrawn as a
  plain inverted palette (no violet, no glass) or removed — see Open questions.
- **Home page**: shorter; a line of positioning, the current role, the work
  block, the three ways to get in touch.
- **Work page**: new public route, FR and EN in strict parity, declared in the
  content schema like the other pages (`resources/content/pages/{fr,en}/work.md`,
  `ExperienceSchemas` / `PageSchemas`). Entries: astralmanach (library, API, the
  three tools), Un art voulu voyant (uavv.fr), florianrosinski.fr, Atlas
  Dépannage. Each: what it is, what the owner did, one link.
- **SEO**: `Person` JSON-LD gets `sameAs` for GitHub, astralmanach.eu and
  uavv.fr, `address` limited to locality and region (Nancy, Grand Est), and the
  Work page is added to the sitemap.
- **Measurement**: the GA4 identifier is added to the existing consent
  orchestration as a provider, not as a pasted tag; it does not load, and sends
  no signal, before consent (`docs/rgpd`, `config/consent.php`).
- **Documentation**: the constitution (Principle 9), `CLAUDE.md` and
  `AGENTS.md` (the "two themes only" rule), `CONTRIBUTING.md` (design direction),
  `docs/style/*`, `Roadmap.md`.

## Out of scope

- The back office (`/admin`): it keeps working with the new tokens but is not
  redesigned.
- The content model, the articles and case studies: no rewriting, only the
  rendering changes. The owner's career documents (`docs/career/**`) are not
  touched.
- New analytics providers beyond GA4, advertising pixels, session replay.
- Legal identity: the SIRET and a postal address are added to the legal pages
  when the micro-enterprise exists, in a separate change.
- A rename of the repository or of the `--sw-*` token prefix.

## Constraints

- **Constitution, all nine principles**: this is a spec before code (1), privacy
  by default (2), accessibility non-negotiable (3), SEO as architecture (4),
  clarity over cleverness (5), documentation is part of the feature (6), reuse
  (7), quality gates (8), art direction with restraint (9).
- **Principle 9 must be amended first**, explicitly and with its reason: the
  current text names "poetic dusk atmospheres" and "softness inside structure",
  which describe the look being removed. The amendment keeps "restraint" and
  "functional" and drops the atmosphere. No implementation starts before it
  is merged.
- Public copy in French and English in strict shape parity
  (`DeclaredPageContentTest`, `LanguageFileParityTest`); code, docs and keys in
  English.
- No CSS framework; tokens only (`--sw-*`); no hard-coded colour, font, spacing
  or motion in a component; `@layer` and platform primitives preferred.
- Contrast: body text and every interactive state at 4.5:1 or better in every
  theme that ships; focus always visible; `prefers-reduced-motion` respected;
  motion limited to opacity, 0.12 s, linear — no slide, no bounce.
- Fonts self-hosted, subset, `font-display: swap`; Switzer is free (Indian Type
  Foundry) and DM Mono is OFL; their licences are added to the colophon.
- Performance budget unchanged (Core Web Vitals, `npm run audit:lighthouse`);
  the refresh must not regress it, and should improve it (no blur layers).
- Claims on the Work page must be verifiable from the link they carry. No
  client names, figures or testimonials that the owner has not approved.
- Quality gates before any push: `npm run check`, `composer run lint:check`,
  `php artisan test`, `npm run build`, and the SSR build.

## Acceptance criteria

- [ ] Principle 9 of the constitution, `CLAUDE.md`, `AGENTS.md` and
      `CONTRIBUTING.md` are amended and merged before any visual change.
- [ ] `backdrop-filter` does not appear in `resources/css` or `resources/js`
      (0 occurrences); decorative `gradient(` does not appear (0, apart from
      functional uses listed in `docs/style/tokens.md`).
- [ ] The default theme is light; the first paint is the light theme when the
      system has no preference, and the stored or system choice is applied before
      first render (no flash).
- [ ] One accent colour only; status colours are documented and used for status
      alone.
- [ ] Typography uses the two voices; Fraunces, Syne and DM Sans are removed from
      the bundle and from `fonts-deferred.css`.
- [ ] The Work page exists in `/fr` and `/en`, is linked from the header and the
      footer, is in the sitemap, carries its own title and description, and every
      entry links to its public address.
- [ ] Home page, Work page, Services, Contact and the legal pages pass Lighthouse
      accessibility at 100 on mobile and desktop, in every shipped theme.
- [ ] No page scrolls horizontally at 360 px wide; tables scroll inside their own
      region.
- [ ] `Person` JSON-LD validates (Rich Results test / schema validator) with
      `sameAs` and a locality-only `address`.
- [ ] GA4 loads only after consent, in Consent Mode v2, and the consent banner
      offers equal-weight choices; before consent, no request to Google is made
      (verified in the network panel).
- [ ] `docs/style/*`, `docs/rgpd/*`, `docs/seo/*` and `Roadmap.md` describe the
      result; `LanguageFileParityTest` and `DeclaredPageContentTest` pass.
- [ ] The three sites share one written description of the visual grammar (this
      repository's `docs/style/art-direction.md` points to it), so that a change
      to one is visibly a change to all.

## Open questions (owner's decisions)

1. **Dark theme: keep or drop?** Recommendation: keep one, as a plain inverted
   palette (ink background, paper text, same hairlines), because visitors expect
   it and the sibling sites have one; drop `sunset` and its violet glass. The
   alternative is a single light theme, which removes the cost of testing two.
2. **The accent.** Recommendation: none beyond ink for interface chrome, and the
   link colour set by the reader as on astralmanach.eu; or one single colour
   chosen by the owner (the current orange is the natural candidate). Status colours
   (error, success) stay separate.
3. **Typefaces.** Recommendation: Switzer and DM Mono, the same as the sibling
   sites. The current display face (Fraunces, an expressive serif) is the main
   carrier of the "cultured" tone; dropping it is the largest visible change.
4. **The portrait.** The header shows an illustrated avatar. Recommendation:
   remove it from the header and keep it, small, on the home page or the Contact
   page.
5. **The name.** The home page writes "Ismael" without the diaeresis, the legal
   pages and the sibling sites "Ismaël". Recommendation: "Ismaël Rodmacq"
   everywhere; the URL and identifiers stay ASCII.
6. **Wording for each Work entry**, especially Atlas Dépannage: what exactly the
   owner built and what may be said about it. Until the owner writes or approves
   it, the entry carries a name and a link only.
7. **Language of the header tagline** and whether the current role
   (Jewely / Flippad) stays on the home page as the first thing read, given that
   the goal is a change of role.
8. **GA4 next to the existing measurement design.** The site measures audience
   with a first-party, CNIL-exemptable ping and PostHog EU behind opt-in
   (`docs/architecture/measurement.md`). GA4 (`G-KYTKR38YPX`) is wanted for
   Search Console and Analytics reporting. Recommendation: add it as a provider
   behind the same consent, in Consent Mode v2, and keep the first-party ping
   as the privacy-friendly baseline — or decide that GA4 replaces PostHog, which
   shrinks the consent surface. This needs its own short decision record
   (`.specify/templates/decision-template.md`).
9. **Order of work.** Recommendation: constitution amendment, tokens and
   typography, components, home, Work page, SEO and consent, documentation — each
   a separate, reviewable change.

## Tracking

- Linear: keep the primary issue key in `linear_issue:`
- GitHub Project: mirror `github_project_item`, `github_project_status`, and `release` in `docs/ai/github-project/roadmap-spec-issue-map.md`
- Obsidian: set `obsidian_note` to the repo mirror path under `docs/ai/obsidian/build-journal/`
- Codex execution: use the file-based workflow even if native `/speckit.*` commands are unavailable
