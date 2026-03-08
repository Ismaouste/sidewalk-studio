# Structured Data

The current JSON-LD set is intentionally limited to the types already needed by the v0:

- `Person`
- `WebSite`
- `WebPage`
- `BreadcrumbList`
- `BlogPosting` for writing entries
- `Article` for case studies

Static editorial pages such as `Projects` and `Local` keep the default
`WebPage` + `BreadcrumbList` payload. The payload is generated in PHP so static
pages, archive pages, and detail pages share the same canonical source of
truth.

The public breadcrumb trail should reuse that same PHP breadcrumb source so the
visible UI and the emitted `BreadcrumbList` never drift apart.

Writing and case-study detail pages may now expose an `image` property in
their `BlogPosting` or `Article` schema. The value can point either to a real
featured asset or to the generated SVG placeholder route when no image has
been authored yet.

Journal archive and detail schema now resolve on the `/journal` route tree.
Legacy `/writing` URLs should redirect rather than emit a second structured
data surface for the same content.

Binary document downloads such as the public CV files do not emit JSON-LD and
should not be modeled as standalone content entities in the current release.

The `/data-processing` utility page can keep the default `WebPage` shape for
implementation simplicity, but it should not grow richer editorial schema or
be treated as promoted content.
