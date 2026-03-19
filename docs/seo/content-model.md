# Content Model

## Shared contract

Legacy repo-owned Markdown documents still define the original frontmatter contract, whether they live in:

- `resources/content/pages/<locale>`
- `resources/content/writing/<locale>`
- `resources/content/case-studies/<locale>`

Required fields:

- `title`
- `slug`
- `summary`
- `status`
- `published_at`
- `updated_at`
- `tags`
- `seo_title`
- `seo_description`

Optional editorial/media fields:

- `category`
- `publication_type`
- `accent_tone`
- `featured_image`
- `featured_image_alt`
- `featured_video`
- `canonical_url` or the repo-owned `canonical` alias, both of which may use the `{{site_url}}` placeholder
- `open_graph_image` or the repo-owned `ogImage` alias

## Case-study-only fields

- `client`
- `role`
- `stack`
- `outcomes`

## Database-backed publication contract

The `publications` table owns the public-facing metadata contract:

- `type` as `note`, `journal`, or `case_study`
- locale
- slug
- linked markdown source path
- publish status and publish date
- SEO title / description / robots / canonical URL
- featured image / Open Graph image / featured video
- freeform metadata for publication-type-specific fields

The long-form body is intentionally not duplicated in the database. Runtime reads load it from the linked Markdown file.
The public base URL itself is not hardcoded in repo-owned canonicals anymore; it resolves through `SITE_PUBLIC_URL`, with `{{site_url}}` kept as the Markdown placeholder.

## Hybrid page contract

The `pages` table stores per-page overrides and structured payload blocks:

- page key
- locale
- page title / description
- SEO title / description / robots / canonical URL
- Open Graph image
- structured payload JSON for hero, CTA, intro, or other page-specific blocks

## Runtime shaping

The normalized PHP read layer adds:

- locale
- rendered HTML
- raw markdown body
- excerpt
- reading time
- public URL
- publication type
- accent tone
- resolved image metadata
- generated placeholder URL when no featured image exists

## Locale fallback behavior

- English is the baseline locale for the current release line.
- Page content resolves the requested locale first, then `en`.
- Dedicated French page sources currently exist for `home`, `local`, `projects`, and `contact`, plus the internal `experience` content source reused inside the canonical `/projects` work page.
- Dedicated French collection entries now also exist for selected `writing` and `case-studies` slugs.
- Writing and case-study collections resolve the requested locale first, then `en`, then temporary root-level files.
- If multiple collection files share the same slug, the first match in that fallback order wins.

## Page composition fields

Page payloads are allowed to carry structured sections used by the Inertia pages directly. Current examples include:

- `professional_sections`, `associative_sections`, `associative_note_widget`, `side_project_sections`, and `side_projects_widget` inside the internal `experience` content source reused by `/projects`
- `journal_section`, `engagements_intro`, `engagements`, and `notes_section` inside `local`

These are page-specific blocks, not collection-wide requirements, but they are treated as repo-owned content model and should stay documented when extended.
