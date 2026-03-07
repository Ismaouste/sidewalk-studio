# Metadata Strategy

Metadata is generated on the backend and passed twice:

- as Blade view data for the first HTML response
- as Inertia props for client-side navigation updates

## Required fields

- title
- description
- canonical URL
- robots
- Open Graph payload
- Twitter payload
- JSON-LD array

This keeps v0 SEO usable without turning on the SSR runtime.
