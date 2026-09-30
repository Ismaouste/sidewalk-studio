# Analytics Modes

## Now

- **Google Analytics 4** (`GA_MEASUREMENT_ID`), for Search Console and Analytics
  reporting, in Consent Mode v2 **basic**: before the `analytics` category is
  accepted, `gtag.js` is not in the page and no request goes to Google. After
  acceptance it loads once, with `analytics_storage` granted and the ad signals
  denied. A refusal sets Google's `ga-disable-<id>` flag and removes the `_ga`
  cookies. Code: `resources/js/lib/analytics.ts` (`enableGa4`, `disableGa4`).
- `ANALYTICS_DRIVER` names the product-analytics tool (`posthog` or `none`); it
  does not gate GA4, which has its own identifier.
- the first-party audience ping (cookieless, truncated IP) stays the baseline
- the consent buttons carry equal weight

Two gotchas seen in production: `gtag.js` reads `arguments` objects from the
data layer, never arrays; and the consent library hides its banner from
automated browsers (`navigator.webdriver`), so a test must mask it.

## v0 (kept for history)

- `ANALYTICS_DRIVER=none`
- no analytics script is loaded
- the analytics category still exists so the future adapter contract is stable
- any future heatmap or session-replay mode must stay disabled by default and appear only behind explicit consent
- the consent copy for this category should stay localized with the page locale

## Not analytics: what the site remembers locally

`resources/js/composables/useLocalMemory.ts` keeps two things in the reader's
own browser — when they were last here, and how far they had read into a given
article. It is named here so it is not later mistaken for measurement and
folded into a consent category it does not belong to.

The distinction is not a matter of degree. Nothing is sent anywhere: no cookie
is set, no request carries it, no identifier exists, and the server holds no
record. The site cannot read it, count it, or join it to anything. What it
does is let a page be more useful to the person in front of it, which is the
same thing a browser's own scroll restoration does.

That is also why it is worth having. The privacy position is stated in prose
on `/data-processing`; these two features let a visitor check it in devtools
instead — clear site data, watch the memory disappear, see no request carrying
it. If a future feature cannot survive that test, it belongs in a consent
category rather than here.

## Planned later

- Matomo for privacy-first aggregate measurement
- PostHog stays available for explicit opt-in product analytics

Those later adapters should plug into the existing registry instead of coupling themselves directly to the UI modal.
