# Measurement — consent tiers and funnel stages

Shipped with S2 of the commercial repositioning
(`docs/superpowers/specs/2026-09-01-commercial-repositioning-design.md` §6).
This doc is the operator's view; the visitor-facing twin is the
`measurement` section of `resources/content/pages/{en,fr}/data-processing.md`.

## The tiers

| Tier | What runs                                                            | Gate                                                                           |
| ---- | -------------------------------------------------------------------- | ------------------------------------------------------------------------------ |
| T0   | Local-memory composable (localStorage only), consent cookie, session | None needed; disclosed on `/data-processing`                                   |
| T1   | First-party cookieless audience ping → `POST /audience`              | Exempt by design; client opt-out switch + Global Privacy Control, both honored |
| T2   | PostHog EU Cloud (`posthog-js`, lazy-loaded)                         | `analytics` consent category in the banner                                     |
| T3   | PostHog session replay + heatmaps                                    | Its own explicit switch on `/data-processing` — never part of "Accept all"     |
| T4   | Meta Conversions API relay, Google Ads + Consent Mode v2             | **S4, not yet built.** Will ride a `marketing` category                        |

## The funnel vocabulary

Every measurement, email segment, and ad audience maps to these five stages —
no tool gets to invent its own funnel. Events are captured through the
`capture()` facade in `resources/js/lib/analytics.ts`, which no-ops without
accepted analytics consent.

| Stage | Meaning           | Event                                                                                                                                                                      |
| ----- | ----------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| V0    | Any page view     | `$pageview` (T2) and `audience_ping` (T1), automatic                                                                                                                       |
| V1    | Read an article   | `reader_engaged` `{funnel_stage: 'V1', section: 'journal' \| 'case-studies'}` — fires once when the end of the article enters the viewport (IntersectionObserver sentinel) |
| V2    | Viewed the offer  | `services_viewed` `{funnel_stage: 'V2'}` on `/services` mount                                                                                                              |
| V3    | Reached out       | `lead_intent` `{funnel_stage: 'V3', channel: 'email' \| 'whatsapp'}`                                                                                                       |
| V4    | Signed engagement | An offline fact — recorded manually in PostHog, never by the site                                                                                                          |

## The T1 endpoint

`POST /audience` (`AudiencePingController`) accepts
`{path, locale, referrer?}` and is designed against the CNIL
audience-measurement exemption criteria:

- **Stateless by construction.** The route runs without `StartSession`,
  CSRF, Inertia sharing, response cache, or locale resolution; the response
  carries no `Set-Cookie`. There is no state whose consent could be asked.
- **Truncated IP, daily-rotating digest.** The client IP is truncated
  (IPv4 → /24, IPv6 → /48) before it enters an HMAC keyed on
  `app.key + date`, producing a 16-hex-char `visitor` value that can dedupe
  uniques within one day and cannot be recomputed the next. Raw IPs and
  full user agents never leave the request scope.
- **Referrer reduced to its host**, and dropped entirely when it is our own.
- **Global Privacy Control honored twice** — the client never sends a ping
  when `navigator.globalPrivacyControl` is set, and the server discards any
  request carrying `Sec-GPC: 1`.
- **Opt-out** — a switch on `/data-processing` stores
  `sidewalk:audience-opt-out` in localStorage; the sender checks it before
  every ping.

The client half is `resources/js/lib/audience.ts`: `sendBeacon` (with a
`fetch keepalive` fallback) on initial load and every Inertia `navigate`
event, deduped by path so prefetch double-visits and the initial
navigate/init overlap count once.

## Sinks

Production ships no database, so the endpoint hands each normalized event to
a pluggable sink (`app/Audience/AudienceSink`), bound from
`config('audience.sink')`:

- `log` (default) — one structured `audience.ping` line to the app logger.
  On Vercel that is the function log stream; no external service needed.
- `posthog` — server-side capture to the EU project as `audience_ping` with
  `$process_person_profile: false`, so no person profile is ever created.
  Failures are swallowed (a lost ping is a rounding error; a 500 on the
  ping route would surface in every visitor's console).

## Activation runbook

Everything degrades to a working site with zero env vars. To turn PostHog
on (an env change on Vercel, no deploy):

1. Create a PostHog **EU Cloud** project (eu.posthog.com).
2. Set `ANALYTICS_DRIVER=posthog` and `POSTHOG_KEY=phc_…`
   (`POSTHOG_HOST` defaults to `https://eu.i.posthog.com`).
3. Optionally set `AUDIENCE_SINK=posthog` so T1 pings land in the same
   project instead of the logs.

The PostHog project API key is a public, write-only key by design; it rides
the Inertia consent share so the client can lazy-load `posthog-js` after
consent. `posthog-js` lives in its own lazy chunk — it is absent from every
bundle a non-consenting visitor loads.

Related reading: `docs/rgpd/consent-orchestration.md`,
`docs/rgpd/analytics-modes.md`, `docs/rgpd/cookies-and-iframes.md`.
