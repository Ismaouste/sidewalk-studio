# GA4 beside PostHog and the first-party ping

## Status

Accepted

## Context

The owner wants Search Console and Analytics reporting for isrod.eu. The site
already measures audience with a first-party, cookieless ping and offers
PostHog EU behind opt-in (`docs/architecture/measurement.md`). The consent
layer must stay honest: nothing loads or is sent before an explicit yes.

## Decision

Add Google Analytics 4 as one more provider of the `analytics` category,
**Consent Mode v2 basic** (no `gtag.js` and no request before consent). Keep the
first-party ping as the privacy-friendly baseline and PostHog available. No
advertising pixels, no session replay.

## Consequences

- Positive: Search Console and Analytics reporting; one consent surface; the
  banner's promise ("nothing is sent before your choice") is true.
- Negative: a consenting visitor is measured by two tools; Google is a recipient
  of data (named on `/data-processing`).
- Deferred: dropping PostHog if it stays unused.

## References

- Spec: `specs/018-site-refresh/spec.md`
- Related docs: `docs/rgpd/analytics-modes.md`, `docs/architecture/measurement.md`
