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
