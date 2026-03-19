# Content System

Sidewalk Studio now uses a normalized hybrid content layer with explicit ownership boundaries.

## Sources

Long-form editorial sources still live in locale-aware Markdown folders:

- `resources/content/pages/<locale>`
- `resources/content/writing/<locale>`
- `resources/content/case-studies/<locale>`

Runtime metadata and admin indexing live in the database:

- `publications`
- `publication_type_settings`
- `pages` for structured overrides or hybrid page metadata
- `loader_quotes`

English (`en`) remains the public default today. French (`fr`) source files still exist for the current public page set and for selected writing/case-study entries.

## Publication ownership

- The `publications` table owns identity and runtime metadata: locale, slug, type, status, publish date, SEO, listing metadata, admin organization, dirty rebuild state, and the linked Markdown source path.
- The linked Markdown file owns the long-form body.
- Public rendering and static export consume one normalized DTO built from DB metadata plus Markdown body.
- Frontmatter inside managed Markdown files is intentionally minimal and deterministic. It is informational only; the database wins for metadata.

## Legacy frontmatter

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
- `canonical_url`, with `canonical` accepted as a file-backed alias on repo-owned content
- `open_graph_image`, with `ogImage` accepted as a file-backed alias on repo-owned content
- `schema` as an optional hint for editorial detail pages
- repo-owned canonicals may use the `{{site_url}}` placeholder so the public domain can change from one config value instead of rewriting every Markdown file

## Case study extras

- `client`
- `role`
- `stack`
- `outcomes`

## Normalized rendering flow

1. The read layer asks the database first for pages and publications when persisted rows exist.
2. File-backed Markdown still acts as the fallback and import source of truth for legacy content and portable public mode.
3. Collection content falls back by locale (`fr` -> `en`) without mixing duplicates in public views.
4. Frontmatter and persisted records both normalize into the same runtime array shape.
5. Markdown is rendered to HTML on the backend.
6. Collection items are deduplicated by section, locale, and slug so a stale `note`/`journal` mismatch cannot create two public routes for the same document.
7. Collection items are shaped with locale metadata, computed reading time, publication type (`note`, `journal`, `case_study`), accent tone, and both runtime image placeholders and SEO-oriented Open Graph image references.
8. If no featured image exists for page rendering, a generated SVG placeholder is exposed through `/content-visuals/{section}/{slug}.svg`.
9. Inertia pages, SEO, sitemap generation, and static export all consume that normalized read layer.

## Publication rule

Only entries with `status: published` are exposed publicly.
Draft content may exist in the filesystem or database without leaking into the index pages or sitemap.

## Admin and import workflow

- Existing repo-owned publications can be imported into the database through `Database\Seeders\ContentFoundationSeeder` without rewriting their committed files.
- The admin shell edits publication metadata in the database and writes long-form bodies directly to the linked Markdown path.
- Static pages use a hybrid model: structured admin overrides live in `pages`, but file-backed defaults remain available for repo portability.
- Site-level language copy remains file-backed under `lang/*/site.php`, but the admin now writes those files through a structured form instead of requiring direct PHP edits.

## Runtime presentation modules

The database-first runtime layer now also drives:

- theme defaults
- branding asset and fallback variant selection
- loader quotes / flash lines
- publication-type CTA and accent settings
- rebuild/export state

## Locale strategy

- Locale folders remain the content-source boundary, but public page URLs now use locale-prefixed routes such as `/en/projects` and `/fr/contact`.
- Legacy non-prefixed public URLs should redirect to the preferred locale instead of staying canonical.
- Page content resolves locale for those legacy redirects in this order: persisted locale cookie, then browser `Accept-Language`, then `en`.
- Page content still resolves `pages/<locale>/<page>.md` with fallback to `pages/en/<page>.md`.
- Core page sources now exist in both `pages/en/` and `pages/fr/` for `home`, `experience`, `local`, `projects`, and `contact`, but the public work surface is now consolidated on `/projects`.
- The public language switcher is only exposed on routes that already have dedicated French page sources or localized collection entries for the current route. Unsupported routes stay visibly English until translated content exists.
- Writing and case-study detail routes can still fall back to English, but collection and widget listings should stay locale-pure instead of mixing FR and EN in the same index view.
- Slug deduplication happens after resolution, so a localized entry can override the English document for the same slug.
- Root-level collection files are transitional only and should be moved into `en/` when touched.

## Public work surface

- `/{locale}/projects` is the canonical public work page.
- `/{locale}/experience` and `/experience` remain legacy redirects only.
- The `experience` page markdown source still exists as a content fragment reused by the `/projects` controller so recruiter-facing and project-facing material can stay modular without exposing two public routes.

## Public editorial surface

- `/{locale}/local` now acts as the main editorial page for place, civic context, journal highlights, and the simple notes listing.
- `/{locale}/journal` is the canonical journal archive and is exposed from the primary navigation.
- Legacy `/writing` URLs redirect to the locale-prefixed journal route.
- Page frontmatter can therefore include structural fragments such as `professional_sections`, `associative_sections`, `associative_note_widget`, `side_project_sections`, `side_projects_widget`, `journal_section`, `engagements_intro`, `engagements`, and `notes_section` to compose public pages from repo-owned content blocks instead of adding more routes.

## Future remote content position

- Writing and Case Studies remain repo-versioned for the current release line.
- Sanity is explicitly out of scope for the current content system.
- If Sanity is explored later, it should start with a separate editorial domain rather than replacing the current Markdown collections immediately.
- Any future remote source must normalize into application-owned content shapes before controllers, SEO, and sitemap logic consume it.

See `docs/architecture/sanity-content-strategy.md` for the future adoption boundary.
