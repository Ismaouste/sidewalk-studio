# Metadata Strategy

Metadata is generated on the backend and passed twice:

- as Blade view data for the first HTML response
- as Inertia props for client-side navigation updates

## Resolution order

1. Page or publication values persisted through the admin shell
2. Linked Markdown body or legacy file-backed frontmatter fallback
3. Site-wide defaults from `App\Services\SiteSettingsService`

Site-wide defaults such as the title suffix, default description, and public profile links resolve through `SiteSettingsService`, which still falls back to committed defaults when no persisted row exists yet.

## Required fields

- title
- description
- canonical URL
- robots
- Open Graph payload
- Twitter payload
- JSON-LD array
- UI breadcrumb payload derived from the same backend breadcrumb source

## Current rules

- static editorial pages such as `Projects` and `Local` use the same backend pipeline as the archives and detail pages
- page metadata now declares a schema variant instead of always emitting a generic `WebPage`
- writing detail pages emit `Article` JSON-LD and `og:type=article`
- case-study detail pages emit `CreativeWork` JSON-LD and keep `og:type=website`
- `/{locale}/projects` is the public about/experience surface and emits a recruiter-facing `Person` payload
- Open Graph image precedence is: explicit metadata image, per-slug `/images/og/{slug}.jpg`, then `/images/og/site-default.jpg`
- collection detail pages can still carry a content image for UI rendering, with a generated SVG placeholder as fallback when needed
- binary download endpoints such as `/cv/en` and `/cv/fr` are utility routes and should stay out of page-level metadata flows
- `/contact` is public but intentionally `noindex,follow`
- `/data-processing` remains `noindex,nofollow`
- canonical public URLs now resolve on locale-prefixed routes such as `/en/projects` and `/fr/journal`
- legacy non-prefixed public URLs should redirect to the locale-prefixed canonical route instead of emitting a second metadata surface
- the public title format is `[Page] · Ismael Rodmacq`, while the site root can still keep the bare portfolio name when appropriate
- request-level locale negotiation should choose the redirect target for legacy URLs, but must not emit duplicate canonical paths or `hreflang` trees yet
- legacy `/writing` paths redirect to `/journal` instead of emitting duplicate metadata

## Admin-managed metadata

The admin shell can now manage:

- per-publication SEO title and description
- per-publication robots and canonical URL
- per-page SEO title and description
- per-page robots and canonical URL
- per-page Open Graph image references

These values must stay compatible with both live Laravel rendering and the static export flow.

Publication metadata now lives primarily in the database. Markdown remains the long-form body source, not the canonical SEO metadata source.
Repo-owned Markdown may still declare file-backed aliases such as `canonical` and `ogImage`; the content repository normalizes them into the same runtime metadata keys.
