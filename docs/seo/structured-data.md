# Structured Data

The current JSON-LD set stays intentionally limited to the types already needed by Sidewalk Studio:

- `Person`
- `WebSite`
- `WebPage`
- `BreadcrumbList`
- `BlogPosting` for writing entries
- `Article` for case studies

## Source of truth

Structured-data generation should keep reading normalized page/publication payloads rather than reaching directly into file frontmatter.

That means:

- publication metadata such as title, summary, dates, image references, and canonical intent can now come from database-managed records
- case-study-specific metadata remains available through the publication `metadata` payload
- static editorial pages such as `Projects` and `Local` keep the default `WebPage` + `BreadcrumbList` payload
- visible breadcrumbs must reuse the same PHP breadcrumb source as `BreadcrumbList`

## Current rules

- writing and case-study detail pages may expose an `image` property using either a real featured asset or the generated SVG placeholder route
- journal archive and detail schema resolve on the `/journal` route tree only
- legacy `/writing` URLs redirect rather than emitting a second structured-data surface
- binary document downloads do not emit JSON-LD
- `/data-processing` can keep the default `WebPage` shape, but should not grow richer editorial schema

## Dual-mode requirement

Static export should continue to render the same JSON-LD decisions as the live Laravel runtime because both modes now share the same normalized read layer.
