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

Binary document downloads such as the public CV files do not emit JSON-LD and
should not be modeled as standalone content entities in the current release.

The `/data-processing` utility page can keep the default `WebPage` shape for
implementation simplicity, but it should not grow richer editorial schema or
be treated as promoted content.
