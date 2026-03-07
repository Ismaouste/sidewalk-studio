# Content Model

## Shared contract

Every Markdown document must define the same frontmatter contract, whether it lives in:

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

## Case-study-only fields

- `client`
- `role`
- `stack`
- `outcomes`

## Runtime shaping

The PHP repository adds:

- locale
- rendered HTML
- excerpt
- reading time
- public URL

## Locale fallback behavior

- English is the baseline locale for the current release line.
- Page content resolves the requested locale first, then `en`.
- Writing and case-study collections resolve the requested locale first, then `en`, then temporary root-level files.
- If multiple collection files share the same slug, the first match in that fallback order wins.
