# Content System

Content lives in `resources/content/writing` and `resources/content/case-studies`.
Each entry is a Markdown file with required frontmatter.

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

1. PHP loads and validates the frontmatter.
2. Markdown is rendered to HTML on the backend.
3. Inertia pages receive already-shaped content arrays.
4. SEO and sitemap logic reuse the same content source.

## Publication rule

Only entries with `status: published` are exposed publicly.
Draft content may exist in the filesystem without leaking into the index pages or sitemap.
