# Structured Data

The current JSON-LD set stays intentionally limited to the types already needed by Sidewalk Studio:

- `WebSite`
- `WebPage`
- `Person` for `/{locale}/projects`
- `BreadcrumbList`
- `Article` for writing entries
- `CreativeWork` for case studies

## Source of truth

Structured-data generation should keep reading normalized page/publication payloads rather than reaching directly into file frontmatter.

That means:

- publication metadata such as title, summary, dates, image references, and canonical intent can now come from database-managed records
- the long-form article body still comes from the linked Markdown file referenced by that record
- case-study-specific metadata remains available through the publication `metadata` payload
- repo-owned canonical values may contain `{{site_url}}`, which resolves through `SITE_PUBLIC_URL` before JSON-LD is emitted
- static editorial pages such as `Home`, `Local`, and `Journal` keep the default `WebPage` + `BreadcrumbList` payload
- `/{locale}/projects` is the exception and emits a `Person` schema with job title, email, address, `sameAs`, and `knowsAbout`
- visible breadcrumbs must reuse the same PHP breadcrumb source as `BreadcrumbList`

## Current rules

- writing detail pages expose `Article` with `headline`, description, publish/update dates, nested author and publisher, `mainEntityOfPage`, and an Open Graph image fallback chain
- case-study detail pages expose `CreativeWork` with description, author, `dateCreated`, keywords, URL, and an Open Graph fallback image
- journal archive and detail schema resolve on the locale-prefixed `/{locale}/journal` route tree only
- legacy `/writing` URLs redirect rather than emitting a second structured-data surface
- binary document downloads do not emit JSON-LD
- `/data-processing` can keep the default `WebPage` shape, but should not grow richer editorial schema
- `/{locale}/sparkle` keeps a plain `WebPage` payload and remains intentionally non-indexed
- the `Person` payload must keep `name`, `jobTitle`, `url`, `email`, `sameAs`, `address`, and `knowsAbout` populated from site settings and author defaults

## Dual-mode requirement

Static export should continue to render the same JSON-LD decisions as the live Laravel runtime because both modes now share the same normalized read layer.
