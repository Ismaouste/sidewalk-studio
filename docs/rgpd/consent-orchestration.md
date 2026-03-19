# Consent Orchestration

The consent layer exposes a small, explicit contract:

- `necessary`
- `analytics`
- `media`

CookieConsent owns preference capture and persistence.
IframeManager owns iframe blocking and loading.
An internal registry connects those tools to future scripts and embeds.

## Current rule

Analytics stays on the `none` driver in v0.
The architecture is ready for real drivers later, but the public site does not load Matomo or PostHog yet.

The public footer is the stable manual entry point to reopen consent
preferences. If optional consent tooling is blocked client-side, the rest of
the app must still render normally.

The consent modal and the iframe notices must follow the current public locale.
English and French copy should stay aligned so the contract remains readable in
both versions of the site.
