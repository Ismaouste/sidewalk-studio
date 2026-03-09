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
- collection detail pages can carry a content image in metadata, with a generated SVG placeholder as fallback when needed
- binary download endpoints such as `/cv/en` and `/cv/fr` are utility routes and should stay out of page-level metadata flows
- `/data-processing` remains `noindex,nofollow`
- request-level locale negotiation must not create duplicate route trees, `hreflang`, or alternate canonical paths until a fuller multilingual SEO model exists
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
