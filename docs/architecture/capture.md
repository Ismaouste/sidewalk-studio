# Capture — newsletter, audit lead magnet, booking

How stage V3 (Lead) is captured, on the funnel vocabulary from
`measurement.md`. Everything here degrades to a working site with zero env
vars: signups log a masked line, audit mails render into the function logs,
and the booking block stays hidden until a URL exists. Activation is
configuration, never a code change.

## The surfaces

| Surface           | Where                                   | Segment   | Event                                   |
| ----------------- | --------------------------------------- | --------- | --------------------------------------- |
| Newsletter block  | Home (low), journal posts, case studies | see below | `lead_intent` `{channel: 'newsletter'}` |
| Audit lead magnet | `/labs/audit` (card on `/labs`)         | —         | `lead_intent` `{channel: 'audit'}`      |
| Booking link      | `/contact` (hidden until a URL is set)  | —         | `lead_intent` `{channel: 'booking'}`    |

Segments: `engineering` (journal readers, V1) and `local-business` (Home and
case studies, V2). Two Brevo lists, one `SEGMENT` attribute — no third
segment without a new list and a new placement decision.

## Double opt-in flow

`POST /newsletter` is stateless like `/audience` (no session, no CSRF, no
cookie), throttled (6/min), honeypot-guarded (`company_website`). The
controller hands the address to a `NewsletterDriver`:

- `log` (default): one `newsletter.subscribed` line with the address
  **masked** (`r***@example.com`) — no PII in function logs, ever.
- `brevo`: one call to `contacts/doubleOptinConfirmation`. Brevo sends the
  confirmation email from a DOI template; the contact reaches a list only
  after clicking; the click lands on `/{locale}/newsletter/confirmed`
  (noindex, translated). Without `BREVO_API_KEY` the driver logs
  `newsletter.driver_unconfigured` and drops the signup gracefully.

No address is ever stored on our side.

## The audit pipeline

`POST /labs/audit` (stateless, throttle 3/10 min — each accepted request
costs a PageSpeed run and an email) → one PageSpeed Insights v5 call
(mobile, PERFORMANCE + SEO; the response embeds CrUX field data, so no
second call; the `category` parameter must be repeated, which rules out
`http_build_query`) → `AuditReport::fromPageSpeed()` normalizes scores,
p75 field metrics, a lab snapshot, and the top opportunities → the same
array feeds the markdown mailable (`AuditReportMail`, copy from
`lang/*/public.php` `audit_mail`) and the inline summary on the page.

`vercel.json` sets `maxDuration: 60` because a real PSI run takes 20–35 s.

**The keyless PSI quota is zero in practice** (observed live: instant 429
on every keyless call). Without `PAGESPEED_API_KEY` the endpoint degrades
to a clean `502 {"status":"unavailable"}` and the page shows a retry note —
the free key (25k requests/day) is a hard requirement for the tool to
answer, not a nicety.

## Activation runbook

1. **Brevo** — create the account (free tier), then: two lists
   (_Engineering readers_, _Local business_), a double-opt-in template
   (Campaigns → Templates → must contain the `{{ doubleoptin }}` tag), an
   API key (SMTP & API → API keys). Set on Vercel:
   `NEWSLETTER_DRIVER=brevo`, `BREVO_API_KEY`, `BREVO_DOI_TEMPLATE_ID`,
   `BREVO_LIST_ENGINEERING`, `BREVO_LIST_LOCAL_BUSINESS` (numeric list ids).
2. **Transactional mail (audit reports)** — Brevo SMTP relay (300/day
   free): `MAIL_MAILER=smtp`, `MAIL_HOST=smtp-relay.brevo.com`,
   `MAIL_PORT=587`, `MAIL_USERNAME=<brevo account login>`,
   `MAIL_PASSWORD=<smtp key>`, `MAIL_FROM_ADDRESS=ismael@rodmacq.com`,
   `MAIL_FROM_NAME="Ismaël Rodmacq"`. Until then audit mails render into
   the function logs (`MAIL_MAILER=log` is the default).
3. **PageSpeed key** (required — see above) — Google Cloud console →
   enable "PageSpeed Insights API" → credentials → API key →
   `PAGESPEED_API_KEY` on Vercel. Free, 25k/day.
4. **cal.com** — create the account (free), a 30-minute event type, then
   put the public link into `booking.url` in
   `resources/content/pages/{en,fr}/contact.md` and push. Production
   serves pages from the database bundled at build time, so a content
   edit ships with the deploy; the block renders itself the moment the
   URL is non-empty.

## Sequences (configured in Brevo Automations, not in code)

Draft copy lives here until it lives in Brevo; keep FR/EN in step.

- **V1 welcome — engineering** (list: Engineering readers), 2 emails:
    1. EN "You're in — here's the good stuff" / FR "Bienvenue — le meilleur
       du journal": best-of three journal posts, repo link, reply-to open.
    2. (J+7) EN "How this site measures itself" / FR "Comment ce site se
       mesure lui-même": the consent-first measurement story → journal.
- **V2 audit offer — local business** (list: Local business), 2 emails:
    1. EN "Is your site losing local customers?" / FR "Votre site fait-il
       fuir des clients locaux ?": the audit tool → `/labs/audit`.
    2. (J+5) EN "What €2,900 actually buys" / FR "Ce que 2 900 € achètent
       vraiment": the Site local package, walked through → `/services` and a
       case study.
- **V3 qualification — both lists**, triggered by an audit-request
  attribute, 1 email: EN "Your report, and the 30-minute version" / FR
  "Votre rapport, et la version 30 minutes": booking link → `/contact`.

## Local development notes

- The Windows PHP install shipped without a CA bundle; outbound TLS from
  PHP (PSI, Brevo) failed with cURL error 60 until `curl.cainfo` /
  `openssl.cafile` were pointed at Git's `ca-bundle.crt` in `php.ini`
  (done 2026-09-01 on the dev machine).
- Page content is DB-first (`SITE_CONTENT_SOURCE` defaults to `database`):
  after changing a page's markdown or schema, reseed with
  `PageContentRepository::seededPage()` + `savePage()` — and note that
  `savePage` persists the **nested** `payload` key of what `seededPage`
  returns; editing the flattened top-level copy silently changes nothing.
