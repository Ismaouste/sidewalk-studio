# Content System

Public content lives in locale-aware Markdown folders:

- `resources/content/pages/<locale>`
- `resources/content/writing/<locale>`
- `resources/content/case-studies/<locale>`

English (`en`) is the public default today. French (`fr`) source files now exist for the core markdown-backed pages so future locale exposure can reuse the same repository contract without reshaping the content tree.

## Shared frontmatter

- `title`
- `slug`
- `summary`
- `status`
- `published_at`
- `updated_at`
- `tags`
- `seo_title`
- `seo_description`

## Case study extras

- `client`
- `role`
- `stack`
- `outcomes`

## Rendering flow

1. PHP resolves the requested locale path first.
2. Collection content falls back to `en`, then to the legacy root folder while the migration remains in progress.
3. Frontmatter is validated before the document enters application flows.
4. Markdown is rendered to HTML on the backend.
5. Inertia pages receive already-shaped content arrays with locale metadata.
6. SEO and sitemap logic reuse the same content source.

## Publication rule

Only entries with `status: published` are exposed publicly.
Draft content may exist in the filesystem without leaking into the index pages or sitemap.

## Locale strategy

- Locale folders are an internal content-source boundary for now, not a public routing guarantee.
- Page content resolves locale per request in this order: `?lang=<locale>`, then the persisted locale cookie, then the browser `Accept-Language` header, then `en`.
- Page content still resolves `pages/<locale>/<page>.md` with fallback to `pages/en/<page>.md`.
- Core page sources now exist in both `pages/en/` and `pages/fr/`, but routing and SEO still expose English as the stable public default.
- Writing and case-study collections resolve localized entries first, then English entries, then temporary root-level fallback files.
- Slug deduplication happens after resolution, so a localized entry can override the English document for the same slug.
- Root-level collection files are transitional only and should be moved into `en/` when touched.

## Future remote content position

- Writing and Case Studies remain repo-versioned for the current release line.
- Sanity is explicitly out of scope for the current content system.
- If Sanity is explored later, it should start with a separate editorial domain rather than replacing the current Markdown collections immediately.
- Any future remote source must normalize into application-owned content shapes before controllers, SEO, and sitemap logic consume it.

See `docs/architecture/sanity-content-strategy.md` for the future adoption boundary.
