# Tasks: Declared Content Schema

Grouped by the shippable steps in `plan.md`. Each step can land on its own.

## Step 0 — The live defect

- [ ] Quote the colon-bearing scalar at `resources/content/pages/fr/experience.md:25`
- [ ] Sweep both locales for other unquoted scalars containing a colon-space
- [ ] Check `/fr/projects` in a browser, both themes

## Step 1 — Agnostic pass and the parity hole

- [ ] `AppHeader.vue` and `AppFooter.vue`: read the name from `site.name`,
      already shared through Inertia
- [ ] Portrait alt text in `copy/{en,fr}/pages/contact.ts` off the proper noun
- [ ] Neutral defaults in `config/site.php`, `Seo.php`, `SiteSettingsService.php`
- [ ] CV becomes a settings-addressed asset in `SiteController` and
      `ExportStaticPreviewCommand`, instead of a name-shaped filename
- [ ] `name` and `short_name` in the export manifest read from settings
- [ ] Journal, notes and case-studies intros out of the `SiteController`
      ternaries and into `resources/js/copy/`
- [ ] `Labs.vue` link label and card title into the copy tree
- [ ] Translate the two untranslated `aria-label`s —
      `RelatedItemsStrip.vue:20` and `ContentMetaRow.vue:12` — into the copy
      tree; a French screen-reader user hears them in English today

## Step 2 — Publication schema

- [ ] Declaration mechanism under `app/Content/Schema/`: field types, repeats,
      `itemLabel`
- [ ] Declarations for `writing` and `case-studies`
- [ ] `translation_key` added to publication frontmatter and to the schema —
      required before rows can pair locales
- [ ] `ContentRepository` validates against the declaration instead of its flat
      required-key list
- [ ] Test: every file under `resources/content/{writing,case-studies}/`
      validates against its declaration
- [ ] Reconcile the four optional case-study keys (`canonical`, `ogImage`,
      `schema`, `publication_type`) — declared optional or backfilled

## Step 3 — Page schemas and the parity guarantee

- [ ] Declarations for the 8 page keys, including the two that `/projects`
      composes
- [ ] `PageContentRepository` validates all declared fields, not just
      `seo_title` and `seo_description`
- [ ] Test: every page file validates against its declaration
- [ ] Test: EN and FR resolve to the same shape for every page key — and fails
      against the pre-step-0 state of `fr/experience.md`
- [ ] `i18n-content-parity` skill points at the test rather than a prose
      checklist

## Step 4 — Seeding

- [ ] Seed pages and publications from Markdown, idempotently
- [ ] Seeded values stay addressable, so a revert path exists
- [ ] Test: `migrate:fresh --seed` then render matches the Markdown render

## Step 5 — Precedence

- [ ] Database wins over Markdown in both repositories
- [ ] Rewrite `test_repository_prefers_markdown_over_hybrid_database_records`
      and `test_it_prefers_markdown_over_database_overrides_for_public_pages`
      to the inverse assertion
- [ ] `routesToExport()` follows the new precedence
- [ ] Revert-to-seed in `/admin/pages`, exercised by a test
- [ ] Static export verified end to end after a database edit

## Step 6 — Generated editor

- [ ] Schema exported to the client
- [ ] Sectioned form generated from the declaration, replacing
      `AdminStructuredValueEditor`
- [ ] Repeating groups as `<details>` per item, summarised by `itemLabel`
- [ ] Save blocks on a cross-locale shape difference, naming the field
- [ ] Mobile pass at the narrow breakpoint, both themes

## Step 7 — Guided pass and preview

- [ ] Guided sequence for first authoring, for unsaved pages, and for newly
      declared slots
- [ ] Draft URL rendering the real page as the preview

## Documentation

- [ ] `docs/architecture/configurability-inventory.md`: record the decision and
      correct the "respected everywhere" finding
- [ ] `docs/architecture/sanity-content-strategy.md`: the content model moved
- [ ] Decision record for the precedence reversal under
      `docs/architecture/decisions/`
- [ ] `docs/ai/NEXT_SESSION_PROMPT.md`: PR #20 is merged, and this spec is open

## Validation

- [ ] `npm run check`, `composer run lint:check`, `php artisan test`
- [ ] `npm run build:ssr`
- [ ] Both themes on any admin surface added
- [ ] Baseline still 85 tests / 756 assertions, plus whatever these steps add

## Handoff

- [ ] Changelog entry once the precedence reversal ships, since it changes where
      content comes from
