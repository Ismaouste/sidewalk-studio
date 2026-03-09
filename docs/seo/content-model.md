# Content Model

## Shared contract

Every repo-owned Markdown document still defines the same frontmatter contract, whether it lives in:

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

## Case-study-only fields

- `client`
- `role`
- `stack`
- `outcomes`

## Database-backed publication contract

The `publications` table mirrors the public-facing editorial contract and adds admin-managed fields:

- `type` as `note`, `journal`, or `case_study`
- locale
- slug
- markdown body
- publish status and publish date
- SEO title / description / robots / canonical URL
- featured image / Open Graph image / featured video
- freeform metadata for publication-type-specific fields

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
