---
linear_issue: TODO
github_project_item: TODO
github_project_status: proposed
obsidian_note: TODO
release: v1-release-readiness
title: Release readiness — clean v1 publication
status: proposed
---

# Feature Specification: Release readiness

Final pass before publishing the site under its own domain. Address the concrete gaps surfaced by the pre-release audit on branch `claude/cleanup-analytics-planning-66hiU` without expanding scope or introducing new features.

## Problem

The site is feature-complete and philosophically consistent with the colophon, but a small set of concrete issues sit between the current branch and a publication that lives up to the "tenu comme une infrastructure publique" framing. They fall into three buckets: accessibility, performance hygiene, and discovery for multi-locale search.

## Desired outcome

- A keyboard user can navigate past the nav with a single Tab.
- The heaviest static asset on the site is under ~150 KB or properly modernized with a fallback chain.
- Search engines see explicit FR/EN language alternates for every public route.
- A new developer cloning the repo can install and deploy without reading source to discover hidden env vars.
- The WhatsApp number on `/contact` is the correct one (operator confirmed: ending in **98**, not 08) — already fixed in this branch, pending commit.

## In scope

- **A11y skip link.** Add a visually-hidden-until-focus "Skip to main content" link in `app.blade.php` or the root layout component, with FR/EN copy. Add a matching `id="main-content"` target on the `<main>` element of each Inertia page or in the shared layout. Style via existing tokens (`--sw-border-focus`, theme-aware background).
- **Heavy image fix.** Convert `public/images/contact-avatar.png` (868 KB) to an AVIF + WebP + PNG fallback chain and serve it via `<picture>` from `Contact.vue`. Target: AVIF < 80 KB, WebP < 120 KB, PNG fallback < 250 KB. Visual fidelity must remain unchanged in both `morning` and `sunset` themes.
- **hreflang.** Emit `<link rel="alternate" hreflang="fr" href="…" />`, `hreflang="en"`, and `hreflang="x-default"` in `app.blade.php` for every public route. Wire through `app/Support/Seo.php` so that `seo.canonical` and `seo.alternates` are populated from the routing layer. Sitemap entries should also expose `xhtml:link` alternates.
- **.env.example completeness.** Document `VERCEL` and `SKIP_WAYFINDER_GENERATE` (referenced in `vite.config.ts`) and any other env var read by code but absent from the example. Add a one-line comment on each explaining when it applies.
- **Commit the WhatsApp number fix.** `resources/js/pages/Contact.vue` is modified in working tree; commit before the release branch is merged.
- **Lighthouse baseline capture.** Run `npm run audit:lighthouse` and `npm run audit:lighthouse:mobile` before and after the changes above. Record both reports in `docs/ai/obsidian/build-journal/` so the v1 baseline is documented.

## Out of scope

- Converting the `public/images/og/` JPEGs to modern formats. Sizes (33–55 KB) are already acceptable for social previews, and many platforms still expect JPEG. Revisit if a future audit shows real regressions.
- The choice of analytics driver and the colophon copy update — handled by spec `015-analytics-driver`.
- Heatmaps, GTM, multi-pixel marketing — explicitly deferred. If the operator wants a "lab" space for those, that becomes its own spec scoped to a `/labs` opt-in surface and not part of the v1 release.
- Migration off Vercel Hobby. Operator confirmed staying on the free tier within a 10 €/mo total budget for the publication; revisit if commercial activity grows.
- Constitution rewrites or content tone passes. The audit confirms FR/EN parity holds; copy edits should live in their own PR.

## Constraints

- All visible copy ships in FR and EN with shape parity. Skip-link label included.
- No hardcoded colors, fonts, spacing, or motion values — consume `--sw-*` tokens.
- Both `morning` and `sunset` themes must remain visually consistent after the avatar conversion (test on Contact page).
- `php artisan test`, `npm run types:check`, `npm run lint:check`, `npm run format:check`, `npm run build` must all pass before merge.
- Lighthouse desktop performance score must improve or stay flat vs. the captured baseline. Mobile score must not regress by more than 2 points.
- No new npm dependencies for the image pipeline unless one is already present. Generate AVIF/WebP via a one-off script committed under `scripts/` and check the produced files into `public/` — keep CI lean.

## Acceptance criteria

- [ ] Tabbing from a fresh load on `/` reveals a visible "Skip to main content" / "Aller au contenu principal" link as the first focusable element. Activating it moves focus to `#main-content`.
- [ ] `public/images/contact-avatar.avif` (≤ 80 KB), `.webp` (≤ 120 KB), and a re-encoded `.png` (≤ 250 KB) are present and served via `<picture>` from `Contact.vue`.
- [ ] DevTools network panel on `/contact` shows the AVIF being delivered to modern browsers; total bytes for the avatar request drop from ~868 KB to under 100 KB on a recent Chrome.
- [ ] Every public route ships `<link rel="alternate" hreflang="fr|en|x-default">` tags pointing to the correct counterpart URL, validated on `/`, `/projects`, `/colophon`, `/contact`, `/data-processing` in both locales.
- [ ] `sitemap.xml` exposes `xhtml:link` alternates for FR/EN entries.
- [ ] `.env.example` lists every env var read by `vite.config.ts`, `config/consent.php`, and any `env(...)` call in `config/`. Each has a one-line comment.
- [ ] The pending WhatsApp number fix is committed with a clear message.
- [ ] Lighthouse desktop and mobile reports captured before and after live in `docs/ai/obsidian/build-journal/`.
- [ ] `i18n-parity-reviewer` subagent reports clean for `resources/content/pages/`.
- [ ] `design-conformance-reviewer` subagent reports clean for any modified Vue component (`Contact.vue`, layout) and CSS.

## Tracking

- Linear: keep the primary issue key in `linear_issue:`
- GitHub Project: mirror `github_project_item`, `github_project_status`, and `release` in `docs/ai/github-project/roadmap-spec-issue-map.md`
- Obsidian: set `obsidian_note` to the repo mirror path under `docs/ai/obsidian/build-journal/`
- Codex execution: use the file-based workflow even if native `/speckit.*` commands are unavailable
