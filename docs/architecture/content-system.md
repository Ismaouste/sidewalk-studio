# Content System

Public content lives in locale-aware Markdown folders:

- `resources/content/pages/<locale>`
- `resources/content/writing/<locale>`
- `resources/content/case-studies/<locale>`

English (`en`) is the public default today. French (`fr`) source files now exist for the current public page set (`home`, `local`, `projects`, and `contact`) plus the internal `experience` content fragment that now feeds the consolidated `/projects` page. The first localized editorial footprint also exists for selected `writing` and `case-studies` entries.

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
- `category` for archive/widget grouping
- `publication_type` for CTA/widget filtering when a section needs more than one public stream
- `accent_tone` for placeholder and accent rendering
- `featured_image` for a real asset path or absolute URL
- `featured_image_alt`
- `featured_video` for future editorial media hooks

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
5. Collection items are shaped with locale metadata, computed reading time, publication type, accent tone, and a resolved image descriptor.
6. If no featured image exists, a generated SVG placeholder is exposed through `/content-visuals/{section}/{slug}.svg`.
7. Inertia pages receive already-shaped content arrays.
8. SEO and sitemap logic reuse the same content source.

## Publication rule

Only entries with `status: published` are exposed publicly.
Draft content may exist in the filesystem without leaking into the index pages or sitemap.

## Locale strategy

- Locale folders are an internal content-source boundary for now, not a public routing guarantee.
- Page content resolves locale per request in this order: `?lang=<locale>`, then the persisted locale cookie, then the browser `Accept-Language` header, then `en`.
- Page content still resolves `pages/<locale>/<page>.md` with fallback to `pages/en/<page>.md`.
- Core page sources now exist in both `pages/en/` and `pages/fr/` for `home`, `experience`, `local`, `projects`, and `contact`, but the public work surface is now consolidated on `/projects`.
- The public language switcher is only exposed on routes that already have dedicated French page sources or localized collection entries for the current route. Unsupported routes stay visibly English until translated content exists.
- Writing and case-study collections resolve localized entries first, then English entries, then temporary root-level fallback files.
- Slug deduplication happens after resolution, so a localized entry can override the English document for the same slug.
- Root-level collection files are transitional only and should be moved into `en/` when touched.

## Public work surface

- `/projects` is the canonical public work page.
- `/experience` remains as a legacy redirect only.
- The `experience` page markdown source still exists as a content fragment reused by the `/projects` controller so recruiter-facing and project-facing material can stay modular without exposing two public routes.

## Future remote content position

- Writing and Case Studies remain repo-versioned for the current release line.
- Sanity is explicitly out of scope for the current content system.
- If Sanity is explored later, it should start with a separate editorial domain rather than replacing the current Markdown collections immediately.
- Any future remote source must normalize into application-owned content shapes before controllers, SEO, and sitemap logic consume it.

See `docs/architecture/sanity-content-strategy.md` for the future adoption boundary.
