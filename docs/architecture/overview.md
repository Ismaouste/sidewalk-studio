# Architecture Overview

Sidewalk Studio uses Laravel as the orchestration layer for routing, metadata, content loading, and app configuration.
The UI shell is delivered through Inertia + Vue, while the first request still receives canonical tags, Open Graph data, and JSON-LD from Blade view data.

## Main layers

### Laravel application
- defines routes for public pages, writing, case studies, `robots.txt`, and `sitemap.xml`
- loads Markdown content from `resources/content/`
- generates SEO payloads and injects them into both Inertia props and Blade view data

### Inertia + Vue shell
- renders page navigation and content cards
- consumes shared `site` and `consent` props
- initializes the consent layer once on app boot

### Content layer
- stores writing and case studies as Markdown with required frontmatter
- rejects incomplete or malformed content early in the PHP layer
- provides stable URLs for sitemap, canonical tags, and detail pages

### Consent layer
- combines CookieConsent and IframeManager
- exposes an internal script/embed registry so future analytics providers remain optional
- defaults analytics to `none`

## SSR position

The project keeps the SSR entrypoint and avoids anti-SSR decisions, but the SSR runtime is not part of the normal local workflow yet.
The first response already contains server-rendered metadata, which covers the SEO baseline until the full SSR step is worth the extra operational cost.
