---
linear_issue: TODO
github_project_item: TODO
github_project_status: proposed
obsidian_note: TODO
release: v1-release-readiness
title: Analytics driver — sober first-party measurement aligned with the colophon
status: proposed
---

# Feature Specification: Analytics driver

Wire the first real analytics driver behind the existing consent infrastructure, in the spirit of the colophon and `data-processing.md`: opt-out by default, no third-party tracking before explicit consent, no heatmap in v1.

## Problem

Spec 003 left `ANALYTICS_DRIVER=none` as a placeholder. The consent UI advertises a category that does nothing today, which makes the promise look performative rather than load-bearing. Before publication, the site needs a real driver so that "analytics stays off by default" is a meaningful choice for visitors, not a default with no alternative.

The colophon and `data-processing.md` already commit to:

- opt-in explicit for analytics
- heatmap mentioned as a category that "must stay disabled by default" — it is not promised, only constrained if added
- no contact-form persistence, no third-party scripts before consent

We must extend the existing infrastructure (spec 003) rather than replace it.

## Desired outcome

When a visitor lands on the site:

1. No analytics request fires until they explicitly opt in.
2. If they opt in, a privacy-respecting analytics driver collects aggregate page/event data with no cross-site identifiers and no PII.
3. Performance telemetry (Core Web Vitals) is captured via a separate, anonymous channel that does not depend on the analytics consent category — clearly documented in the colophon and `data-processing.md`.
4. The colophon and `data-processing.md` are updated to name the actual driver chosen, not "reserved for future adapters".

## In scope

- Extend `ConsentConfig.driver` type and `config/consent.php` enum to include the chosen sober driver(s): `plausible` and/or `umami`.
- Implement the JS adapter that the existing `registerScript('analytics-driver', ...)` listener will load on opt-in and unload on revocation. Adapter must respect `sidewalk:analytics:enabled` / `sidewalk:analytics:disabled` custom events.
- Add an optional Vercel Speed Insights integration as a perf-only channel, documented separately from analytics consent. Must collect Web Vitals only, no path/query strings beyond what is required for routing aggregation.
- Update FR + EN `data-processing.md` to name the active driver and link to its privacy policy.
- Update FR + EN `colophon.md` "Vie privée / Privacy" section to reflect the real state.
- Add `ANALYTICS_DRIVER` and any required public host/site-id env vars to `.env.example` with comments.

## Out of scope

- Heatmaps and session recording (Hotjar, Microsoft Clarity, Matomo Heatmaps). These break the sober posture and are deferred to a future, clearly-labeled "lab" spec.
- GTM, GA4, Bing UET, TikTok Pixel, and any consent-gated marketing pixel hub. Same rationale.
- Server-side proxying of the analytics endpoint (interesting for ad-blocker resilience but a different spec).
- A custom consent banner — keep `vanilla-cookieconsent` as wired by spec 003.

## Constraints

- Budget ceiling 10 €/month total infrastructure (operator stated).
- `ANALYTICS_DRIVER=none` must remain a supported value and must remain the local-dev default.
- The driver script must NOT load on first paint when consent is unknown or refused. Verified via DevTools network panel: 0 requests to the analytics origin until "Accept" is clicked.
- No cookies set by the analytics driver. If the chosen vendor sets any cookie even in "cookieless" mode, document it explicitly in `data-processing.md`.
- The driver must support a `Do Not Track` honoring mode or the equivalent — if not natively, the adapter wraps the script and refuses to load when DNT=1.
- Operator domain stays the only first-party origin; no third-party CDN for the driver script unless it is the vendor's official cookieless endpoint.
- Both `morning` and `sunset` themes must work unchanged. The driver has no UI surface beyond the existing consent modal.
- All visitor-facing copy ships in FR and EN with shape parity.

## Driver candidates (decision deferred to plan.md)

| Driver | Cost (10€/mo cap) | Heatmap | Cookies | Self-host effort | RGPD posture |
|---|---|---|---|---|---|
| **Plausible Cloud Personal** | 9 €/mo, 10k pageviews | No | None | None | EU-hosted, no consent needed under most readings — still gated for safety |
| **Plausible self-hosted** | 0 € on Fly.io free tier or 5 €/mo on a small VPS | No | None | Medium (Docker + Postgres + ClickHouse) | Full control |
| **Umami self-hosted** | 0 € on Vercel + Neon free Postgres | No | None | Low (Next.js app, deploys to Vercel directly) | Full control |
| **Vercel Web Analytics** | Free up to 2.5k events/mo on Hobby | No | None claimed | None | Aggregated only, US-hosted parent — gate it for safety |
| Matomo Cloud (rejected for v1) | 29 €/mo entry tier | Yes | Optional | None | EU-hosted, RGPD-native — but over budget and brings heatmap which we explicitly exclude |

Decision criteria, in order: (1) zero PII, (2) zero cookie, (3) fits 10 €/mo, (4) low ops burden, (5) data exportable.

## Acceptance criteria

- [ ] `ANALYTICS_DRIVER` accepts at least one real value beyond `none` and the type is updated in `resources/js/types/site.ts` and `config/consent.php`.
- [ ] With consent refused (default): network panel shows zero requests to the analytics origin across `/`, `/projects`, `/colophon`, `/contact`, `/data-processing` in both locales.
- [ ] With consent accepted: a single page-view event fires per navigation, with no query string, fragment, or PII in the payload.
- [ ] Revoking consent via the preferences modal stops further beacons within the same session (verified via the `sidewalk:analytics:disabled` event handler).
- [ ] DNT=1 prevents the driver from initializing, with or without consent.
- [ ] `data-processing.md` (FR + EN) names the active driver and lists which data is sent.
- [ ] `colophon.md` (FR + EN) "Vie privée / Privacy" section reflects the real state.
- [ ] FR/EN content parity check passes (`i18n-parity-reviewer` subagent or skill).
- [ ] Optional: Vercel Speed Insights enabled and documented as perf-only, separate from analytics consent.
- [ ] Lighthouse `--preset=desktop` performance score does not regress by more than 2 points vs. baseline on `/` and `/projects`.

## Tracking

- Linear: keep the primary issue key in `linear_issue:`
- GitHub Project: mirror `github_project_item`, `github_project_status`, and `release` in `docs/ai/github-project/roadmap-spec-issue-map.md`
- Obsidian: set `obsidian_note` to the repo mirror path under `docs/ai/obsidian/build-journal/`
- Codex execution: use the file-based workflow even if native `/speckit.*` commands are unavailable

## Open questions for plan.md

1. **Plausible Cloud vs. Umami self-hosted on Vercel** — Plausible is 9 €/mo zero-ops and well-documented in privacy law publications, Umami is 0 € but adds a service to operate. Recommend Plausible Cloud at this budget unless operator wants to use the analytics service itself as a portfolio artifact.
2. **Vercel Speed Insights perf-only** — enable from day one? Risk: even anonymous Web Vitals travel to Vercel; opportunity: real perf data on production traffic without burdening the analytics consent flow.
3. **Future "lab" spec** — should heatmap / GTM / multi-pixel experimentation become its own spec under a clearly opt-in `/labs` surface? The repo already has `resources/js/pages/Labs.vue`, which suggests the surface exists. To be confirmed and scoped separately.
