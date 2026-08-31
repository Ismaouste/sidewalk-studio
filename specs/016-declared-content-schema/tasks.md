# Tasks: Declared Content Schema

Grouped by the shippable steps in `plan.md`. Each step can land on its own.

## Step 0 — The live defect

- [x] Quote the colon-bearing scalar at `resources/content/pages/fr/experience.md:25`
- [x] Sweep both locales for other unquoted scalars containing a colon-space
- [x] Check `/fr/projects` in a browser, both themes

## Step 1 — Agnostic pass and the parity hole

- [x] `AppHeader.vue` and `AppFooter.vue`: read the name from `site.name`,
      already shared through Inertia
- [x] Portrait alt text in `copy/{en,fr}/pages/contact.ts` off the proper noun
- [x] Neutral defaults in `config/site.php`, `Seo.php`, `SiteSettingsService.php`
- [x] CV becomes a settings-addressed asset in `SiteController` and
      `ExportStaticPreviewCommand`, instead of a name-shaped filename
- [x] `name` and `short_name` in the export manifest read from settings
- [x] Journal, notes and case-studies intros out of the `SiteController`
      ternaries and into `resources/js/copy/`
- [x] `Labs.vue` link label and card title into the copy tree
- [x] Translate the two untranslated `aria-label`s —
      `RelatedItemsStrip.vue:20` and `ContentMetaRow.vue:12` — into the copy
      tree; a French screen-reader user hears them in English today

## Step 2 — Publication schema

- [x] Declaration mechanism under `app/Content/Schema/`: field types, repeats,
      `itemLabel`
- [x] Declarations for `writing` and `case-studies`
- [x] `translation_key` added to publication frontmatter and to the schema —
      required before rows can pair locales
- [x] `ContentRepository` validates against the declaration instead of its flat
      required-key list
- [x] Test: every file under `resources/content/{writing,case-studies}/`
      validates against its declaration
- [x] Reconcile the four optional case-study keys (`canonical`, `ogImage`,
      `schema`, `publication_type`) — declared optional or backfilled

## Step 3 — Page schemas and the parity guarantee

- [x] Declarations for the 8 page keys, including the two that `/projects`
      composes
- [x] `PageContentRepository` validates all declared fields, not just
      `seo_title` and `seo_description`
- [x] Test: every page file validates against its declaration
- [x] Test: EN and FR resolve to the same shape for every page key — and fails
      against the pre-step-0 state of `fr/experience.md`
- [x] `i18n-content-parity` skill points at the test rather than a prose
      checklist

## Step 4 — Seeding

- [x] Seed pages and publications from Markdown, idempotently
- [x] Seeded values stay addressable, so a revert path exists
- [x] Test: `migrate:fresh --seed` then render matches the Markdown render

## Step 5 — Precedence

- [x] Database wins over Markdown in both repositories
- [x] Rewrite `test_repository_prefers_markdown_over_hybrid_database_records`
      and `test_it_prefers_markdown_over_database_overrides_for_public_pages`
      to the inverse assertion
- [x] `routesToExport()` follows the new precedence
- [x] Revert-to-seed in `/admin/pages`, exercised by a test
- [x] Static export verified end to end after a database edit

## Step 6 — Generated editor

- [x] Schema exported to the client
- [x] Sectioned form generated from the declaration, replacing
      `AdminStructuredValueEditor`
- [x] Repeating groups as `<details>` per item, summarised by `itemLabel`
- [x] Save blocks on a cross-locale shape difference, naming the field
- [x] Mobile pass at the narrow breakpoint, both themes

## Step 7 — Guided pass and preview

- [x] Guided sequence for first authoring, for unsaved pages, and for newly
      declared slots
- [x] Draft URL rendering the real page as the preview

## Documentation

- [x] `docs/architecture/configurability-inventory.md`: record the decision and
      correct the "respected everywhere" finding
- [x] `docs/architecture/sanity-content-strategy.md`: the content model moved
- [x] Decision record for the precedence reversal under
      `docs/architecture/decisions/`
- [x] `docs/ai/NEXT_SESSION_PROMPT.md`: PR #20 is merged, and this spec is open

## Validation

- [x] `npm run check`, `composer run lint:check`, `php artisan test`
- [x] `npm run build:ssr`
- [x] Both themes on any admin surface added
- [x] Baseline still 85 tests / 756 assertions, plus whatever these steps add

## Handoff

- [x] Changelog entry once the precedence reversal ships, since it changes where
      content comes from
