# Metadata Strategy

Metadata is generated on the backend and passed twice:

- as Blade view data for the first HTML response
- as Inertia props for client-side navigation updates

Site-wide defaults such as the title suffix, default description, and public profile links now resolve through `App\Services\SiteSettingsService`, which falls back to the config and env-backed defaults until a `site_settings` row exists.

## Required fields

- title
- description
- canonical URL
- robots
- Open Graph payload
- Twitter payload
- JSON-LD array

This keeps v0 SEO usable without turning on the SSR runtime.

Static editorial pages such as `Experience` and `Local` use the same backend
pipeline as the archives and detail pages. When public routes change, the old
path should redirect to the new canonical path instead of exposing duplicate
metadata.

Binary download endpoints such as `/cv/en` and `/cv/fr` are public utility
routes, not indexable editorial pages. They should stay out of page-level
metadata flows and return explicit `X-Robots-Tag` headers instead.

Public page content can now negotiate between English and French on the same
canonical URLs. The locale policy is request-scoped and must not create
duplicate route trees, `hreflang` output, or alternate canonical paths until a
full multilingual SEO model exists.

The public language switcher should only appear on routes that already have
dedicated translated page sources or localized collection entries for the
current slug/archive. Today that safe surface includes `/`, `/experience`,
`/local`, `/projects`, `/contact`, the translated writing routes, and the
translated case-study routes. Unsupported routes keep the English canonical
experience even when a French preference is stored.
