# Structured Data

The current JSON-LD set is intentionally limited to the types already needed by the v0:

- `Person`
- `WebSite`
- `WebPage`
- `BreadcrumbList`
- `BlogPosting` for writing entries
- `Article` for case studies

Static editorial pages such as `Experience` and `Local` keep the default
`WebPage` + `BreadcrumbList` payload. The payload is generated in PHP so static
pages, archive pages, and detail pages share the same canonical source of
truth.
