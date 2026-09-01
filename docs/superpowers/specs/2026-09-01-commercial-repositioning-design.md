# Commercial repositioning — from portfolio to offer-driven open-source flagship

- **Date:** 2026-09-01
- **Status:** Draft for review. Brainstorm output only — no implementation has started.
- **Decision mode:** Isma delegated the calls for this session. Everything below is
  decided unless explicitly tagged **[VALIDATE]** (business/legal calls only Isma can
  make) or **[NEEDS ASSETS]** (material to collect before a phase can ship).

## 1. Context

Today the site runs an identity funnel: `Hello · Experience · Journal · Contact`.
There is no `/services`, no pricing, no engagement model, no newsletter, no analytics
(deliberately — `ANALYTICS_DRIVER=none`, consent categories stubbed and waiting), and
`/case-studies` — the strongest commercial asset, whose frontmatter already carries
`client`, `role`, `stack[]`, `outcomes[]` — is absent from the primary nav.

Isma's goals, restated:

1. Keep the mobile-first performance machine, push further on bleeding-edge platform
   tech as a living demo.
2. Add a real marketing layer: measurement, newsletter, re-engagement, remarketing by
   funnel stage — under a normal, non-aggressive opt-in with an extra confirmation
   tier for heatmaps/session replay.
3. Rework the funnel into something demonstrative, professional and commercial: a
   price grid for web/e-commerce services, a daily rate for technical direction, and
   negotiated fixed-price/part-time engagements.
4. Turn the repo into a genuine open-source project with the site itself as a public
   case study.
5. Target work: (a) freelance technical-director-plus-full-stack engagements like
   Atlas Dépannage, (b) local commerce web/e-commerce with advanced marketing ops
   (synced product catalogs, Meta DPA, Google Shopping, maybe TikTok; structured
   data, Core Web Vitals), (c) preferential-rate work for local associations and
   public/cultural institutions (independent music venues, museums), and
   microbreweries — some of it as a duo with a local video creative.

Three proof pillars are available, each on a different axis:

| Pillar                      | Axis                                                                                                 | Constraint                                                                                           |
| --------------------------- | ---------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------- |
| Atlas Dépannage             | B2B platform, de-facto CTO role (state machine, signed documents, business-model pivot, FR/MA legal) | Proprietary, **not** open source — tell the architecture and decisions, never the code               |
| Crown DP (crown-dp.com)     | E-commerce + fully custom CMS, luxury vintage                                                        | **[NEEDS ASSETS]** screenshots, stack notes, metrics; **[VALIDATE]** whether the client can be named |
| Sidewalk Studio (this repo) | Bleeding-edge front-end, consent-first marketing, open source, reproducible                          | Becomes public — see §10                                                                             |

## 2. Positioning

- **Primary brand stays personal:** Ismaël Rodmacq — tech lead heading to CTO,
  engineering depth plus cultural/creative sensibility. This is the existing profile
  line; the repositioning makes it _commercially legible_, it does not replace it.
- **Sidewalk Studio becomes the umbrella** for productized offers and for the duo
  offer with the video creative. The name already exists; no rebrand needed. The
  site header keeps the personal identity; the services page speaks as the studio.
- **Three audiences, one site:** founders/CTOs hiring part-time technical direction;
  local merchants and cultural institutions buying a site, a shop, or campaigns;
  peers/recruiters reading the journal (they validate credibility for the first two).

## 3. Information architecture and funnel

### New primary nav (5 items)

```
Hello (/) · Services (/services) · Case studies (/case-studies) · Journal (/journal) · Contact ✍🏽 (/contact)
```

- `/experience` leaves the primary nav. It stays live, linked from Hello, the CV, and
  the footer — it is the résumé record, not the sales path. The unresolved
  `/experience` vs `/projects` duality (both `experience.md` and `projects.md` exist,
  plus a 301) is collapsed in the same pass, along with the doubled `<title>` suffix
  bug already on the open-recommendations list.
- `/services` is new: the offer and the price grid (§4).
- `/case-studies` is promoted to primary nav and its index reframed commercially
  (outcomes first, stack second).
- **Home** is reworked: value proposition above the fold, three-offer teaser, proof
  strip (Atlas / Crown DP / this site), local anchor (Nancy · Grand Est · Paris ·
  remote), newsletter hook low on the page.
- **Contact** gains a light qualification form: project type, budget band, timeline —
  fed by the same declared-schema machinery the admin already uses. WhatsApp handoff
  stays; a booking link (cal.com) can come later **[VALIDATE tool choice]**.

### Funnel stages (the shared vocabulary for analytics, email, and ads)

| Stage       | Definition                                   | Re-engagement lever                      |
| ----------- | -------------------------------------------- | ---------------------------------------- |
| V0 Visitor  | Any page view                                | none                                     |
| V1 Reader   | Read a journal post or case study            | content upgrades, newsletter             |
| V2 Prospect | Viewed `/services` or pricing                | audit lead magnet, retargeting (T4 only) |
| V3 Lead     | Newsletter signup, contact, or audit request | email sequences                          |
| V4 Client   | Signed engagement                            | referrals, case-study consent            |

Every measurement, email segment, and ad audience in this design maps to these five
stages — no tool gets to invent its own funnel.

## 4. Offer and pricing

Three lines plus two modifiers. All prices are **proposed anchors** for the French
market (Grand Est + remote) and are **[VALIDATE]** — publish as "à partir de" ranges,
never fixed quotes.

| Offer                                                                                                                               | Shape                     | Proposed anchor           |
| ----------------------------------------------------------------------------------------------------------------------------------- | ------------------------- | ------------------------- |
| **Site local** — vitrine for merchants/institutions: design, Core Web Vitals budget, local SEO, structured data, consent done right | Fixed-price package       | from €2,900 HT            |
| **Boutique** — e-commerce: catalog, payments, product feeds (Google Shopping + Meta DPA), server-side tracking                      | Fixed-price package       | from €7,500 HT            |
| **Signal** — growth retainer: catalog syncs, DPA/Shopping campaigns, structured data, CWV + SEO reporting                           | Monthly retainer          | from €900 HT/month        |
| **Direction technique** — part-time CTO / tech lead + full-stack (the Atlas shape)                                                  | Daily rate, 1–3 days/week | TJM €650 HT (target €750) |
| **Forfait plateforme** — scoped product builds                                                                                      | Negotiated fixed price    | on quote                  |

Modifiers:

- **Associations:** −30% on packages. Cultural institutions and independent music
  venues: adapted quote, stated explicitly on the page (it is a positioning signal,
  not a discount buried in email).
- **Duo studio** (with the video creative): content production + engineering bundles
  for microbreweries, venues, museums — on quote, presented as its own block with
  her credited **[VALIDATE with her: name, terms, revenue split]**.

Business admin: current micro-entrepreneur revenue ceilings and the 2025–2026 TVA
franchise threshold changes must be checked against the mix of retainers + packages
before the grid is published **[VALIDATE with up-to-date figures]**.

## 5. Proof: case studies

- **Atlas Dépannage** — the flagship "directeur technique ET dev full stack" story:
  the pivot (mirror, new free-tier infra, virgin DB), the 5-phase mission state
  machine and its sealed guards, signed-document flows, the inverted money flow, the
  FR/MA legal identity work. Architecture diagrams and product decisions only; no
  proprietary code, no client data. **[VALIDATE]** what the director allows: naming,
  screenshots, which production numbers may be public.
- **Crown DP** — the e-commerce/CMS proof aimed exactly at the local-commerce target:
  custom CMS, catalog, luxury-vintage constraints. **[NEEDS ASSETS]**.
- **Sidewalk Studio** — the open, reproducible one: every phase of this design ships
  with a journal post, and the site publishes its own metrics (§6, "open metrics").

## 6. Measurement and consent tiers

Design principle: the consent UX is itself a portfolio piece — banner with equal
accept/refuse, granular categories, and a _separate, explicit_ confirmation for the
invasive tier. The existing `vanilla-cookieconsent` + `config/consent.php`
infrastructure carries all of it; the `analytics` category finally gets a driver and
a `marketing` category is added.

| Tier                 | What runs                                                                                                                                                                                   | Legal basis / UX                                                                  |
| -------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------- |
| T0 Necessary         | Existing local-memory composable (localStorage only), consent cookie, session                                                                                                               | No consent needed; disclosed in `/data-processing`                                |
| T1 Audience          | First-party cookieless page-count ping to a Laravel endpoint, designed against the CNIL audience-measurement exemption criteria (no cross-site, no cookie, truncated IP, bounded retention) | Exempt by design; disclosed, with an opt-out                                      |
| T2 Product analytics | PostHog **EU Cloud**: funnels on the V0–V4 stages, event capture                                                                                                                            | `analytics` consent in the normal banner                                          |
| T3 Heatmaps / replay | PostHog session recording + heatmaps                                                                                                                                                        | **Separate explicit confirmation** on top of T2 — never bundled into "accept all" |
| T4 Marketing         | Meta Pixel via **Conversions API relay in Laravel** (server-side), Google Ads remarketing, Google Consent Mode v2; ad audiences built per funnel stage                                      | `marketing` consent; V2/V3 stage audiences only                                   |

Why PostHog over Matomo: one EU-hosted tool covers funnels, heatmaps, replay,
surveys and feature flags; its open-source core fits the project narrative; and the
config comment already reserved the slot for "Matomo or PostHog". T1 exists so the
site keeps baseline numbers even at 0% consent — and T1's implementation is itself a
journal post ("a CNIL-exemptable analytics endpoint in ~200 lines of Laravel").

**Open metrics:** a public page (or embedded dashboard) exposing the site's own
traffic and Web Vitals — radical transparency as marketing, coherent with the
open-source flip.

## 7. Re-engagement

- **Newsletter: Brevo** (French company, GDPR-native, free tier, automation,
  syncs audiences toward Meta ads later). Double opt-in. Two segments from day one:
  _engineering readers_ (journal) and _local business_ (services). Signup blocks on
  Home, journal posts, and case studies — stage-aware copy, not a modal.
- **Lead magnet #1 — the automated audit:** a Labs tool where a prospect enters
  their URL, the server pulls PSI/CrUX data, and returns a branded Core Web Vitals +
  SEO mini-report by email. Simultaneously a lead magnet, a tech demo, and a journal
  post. This is the highest-leverage single build in the whole design.
- **Sequences by stage:** V1 → welcome + best-of content; V2 → audit offer +
  relevant case study; V3 → qualification + booking. Written in both locales.
- **Remarketing (T4 consent only):** stage-based audiences (V2 prospects get offer
  ads, V1 readers get content ads). TikTok catalog is deferred until a client
  project needs it — YAGNI **[VALIDATE priority]**.

## 8. Demos, labs, media

- **Labs additions** (each demo doubles as a journal post): the audit tool (§7); a
  product-feed playground (paste a CSV → valid Google Shopping / Meta DPA feed,
  with the diffs explained); extend the existing structured-data playground with
  Event/Product/LocalBusiness presets for venues, museums, shops; keep the consent
  sandbox as the T0–T4 showcase.
- **Platform-primitive demos to add:** customizable `<select>`
  (`appearance: base-select`), Invoker Commands (`commandfor`),
  `interpolate-size`/`calc-size()` height animations, scroll-state container
  queries. CSS anchor positioning stays parked — tried and reverted (db61a48,
  Chromium misplacement) — revisit only when `position-try-fallbacks` fires
  reliably.
- **Vertical demo kits** (fictional but production-grade, filmable with the duo
  partner): a microbrewery shop (age gate done right, click-and-collect, live
  product feed), a music-venue event page (Event JSON-LD, ticket funnel), an
  association donation page (accessibility-first). These are the images/videos that
  illustrate the prospecting — **[NEEDS ASSETS]** shot with the video creative.
- **Media pipeline:** AVIF stills; short demo loops self-hosted (`<video>` +
  poster, lazy, no third party); long-form on YouTube behind the _existing_ `media`
  consent category. No new consent surface needed.

## 9. Content plan — first wave

1. Atlas case study (architecture + decisions narrative).
2. Crown DP case study.
3. "A consent-first marketing stack" series: the tier design, the T1 endpoint, the
   Conversions API relay, Consent Mode v2.
4. "Product feeds are an engineering problem": catalog sync, DPA, Shopping.
5. "Core Web Vitals for local commerce" + the audit tool write-up.
6. One post per platform-primitive demo shipped.
7. Duo videos: vertical demo kit walkthroughs, before/after reels.

## 10. Open-source policy

- **Code: MIT.** Maximizes reuse and credibility for a reference implementation.
- **Content excluded:** `resources/content/**`, CV material, and brand assets stay
  all-rights-reserved (stated in the LICENSE note and README).
- **Before flipping public:** secret scan over full git history, `.env*` audit,
  review of `docs/career/` and anything personal in `docs/`; add CONTRIBUTING.md
  and a public roadmap (GitHub issues + milestones mirroring §11).
- README repositioned: what the project demonstrates, who it serves, how to run it,
  and the case-study index. The current "reusable reference" framing survives; the
  commercial context is added, not hidden.

## 11. Sequencing

Each phase is one spec-kit spec, shipped and pushed to main (deploy-on-push, full
baseline before every push). Order optimizes for "commercial spine first, measure
before you market":

| Phase               | Scope                                                                                                                                                                                    | Depends on                  |
| ------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------- |
| S1 Commercial spine | New nav, `/services` + price grid, case studies promoted, Home rework, contact qualification; fold in hygiene fixes (title suffix, experience/projects collapse, reduced-motion seeding) | pricing validation          |
| S2 Measurement      | Consent tiers T1–T3, PostHog EU, T1 endpoint, funnel-stage events                                                                                                                        | —                           |
| S3 Capture          | Brevo newsletter, audit lead magnet, sequences                                                                                                                                           | S2 (stages)                 |
| S4 Marketing        | T4: Conversions API relay, Google Ads + Consent Mode v2, stage audiences                                                                                                                 | S2, S3                      |
| S5 Show             | Vertical demo kits, labs additions, media pipeline, primitive demos                                                                                                                      | duo assets                  |
| S6 Tell + open      | Case studies Atlas/Crown DP, content wave, open-source flip                                                                                                                              | asset/permission validation |

S1 and S2 can start immediately after this document is approved; nothing in them
blocks on external assets.

## 12. Open questions for Isma

1. Pricing anchors and TJM (§4) — adjust before S1 ships the grid.
2. Atlas: what may be named/shown publicly, and which production numbers.
3. Crown DP: can the client be named; where do assets/repo live.
4. Duo partner: naming, credit, terms.
5. Micro-entrepreneur ceilings / TVA franchise 2026 — verify current figures.
6. Repo public flip timing: with S6, or earlier as motivation?
7. Booking tool (cal.com vs none for now) and TikTok catalog priority.
