# Implementation Plan: Declared Content Schema

## Summary

A schema module declares, per page key and per publication type, the fields
that content holds. Validation, seeding, the admin editor and the locale parity
check all read that one declaration. The Markdown files become seeds; the
database becomes authoritative once seeded.

The sequence below is ordered so the first useful outcome ships the same day
and every later step is independently shippable. The risky step — reversing the
Markdown precedence — lands fifth, after the four steps that build the safety
net it needs.

## Decisions

- **Approach B, per page key.** Reasoning and cost in `spec.md`. The unit is the
  page key, because `/projects` composes two of them.
- **Publications are not part of the approach question.** One shape, 22 files,
  15 always-present fields. They need the precedence flip and a translation
  link, not a schema debate.
- **The declaration lives in PHP**, beside the repositories that already
  validate, and is exported to the client for the admin editor. A single source
  that both the save path and the form generator read.
- **The copy tree stays compiled for now.** Trading a working compile-time
  guarantee for an unbuilt runtime one is the exact trade the constraints
  forbid. It moves once the mechanism has proved itself on pages.

## Section 3 — Markdown versus database precedence

**The answer is: Markdown becomes the seed format, the database becomes
authoritative after seeding.** What that breaks, checked rather than assumed:

**The static export survives, almost for free.** `ExportStaticPreviewCommand`
fetches each route over HTTP from a running server and rewrites the returned
HTML. Rendering therefore follows whatever the application reads, with no
knowledge of the source. The one coupling is `routesToExport()`, which calls
`$content->published()` to enumerate publication URLs — the same repository, so
it follows the precedence change automatically. Risk: low. The guard is the
seed-fidelity test in step 4.

**The locale fallback is already symmetric.** `databaseItemsForSection()`
resolves `[$locale, 'en']` when a fallback is requested, and `fileItems()` does
the same for files. Both sources already implement FR-falls-back-to-EN
identically, so reversing which one wins does not change fallback behaviour.
Risk: low.

**A gap that is independent of precedence and blocks the flip anyway.**
Publication slugs are per-locale and mostly differ — 5 of 11 journal slugs are
shared between EN and FR, 2 of 4 case studies — and **no frontmatter field
links a publication to its translation.** Today nothing needs one, because each
locale is a separate directory. The moment publications are rows, "the French
version of this article" has no expression in the data model. A
`translation_key` must be added to the frontmatter and to the schema _before_
the flip, or the admin cannot pair them.

**Operator recovery.** The audit log behind `/admin/pages` already records
changes. What is missing is a revert-to-seed path: the seeded value stays
addressable after the flip so an operator can restore a page without a
developer. That is an acceptance criterion, not a nice-to-have — it is the
thing that makes an authoritative database safe for a non-developer.

**The two pinning tests are rewritten, not deleted.**
`ContentRepositoryTest::test_repository_prefers_markdown_over_hybrid_database_records`
and
`PageContentRepositoryTest::test_it_prefers_markdown_over_database_overrides_for_public_pages`
become the inverse assertion — a database row overrides a file that still
exists — and a third test is added asserting that seeding from Markdown then
rendering produces what the Markdown rendered before. The current tests
document a deliberate past decision; their replacements document the new one.

## Section 4 — The guided editor, pressure-tested

The requested experience is mobile-first, question-led and prefilled. Audited
as a design, three parts of it hold and two need changing.

**A wizard is the wrong default surface, and the numbers say so.** The
experience record alone has 64 leaf fields. Walking 64 questions to fix one
typo is worse than a form in every way. The declaration generates _both_
surfaces from the same source: a **sectioned form** is the default for editing,
and a **guided pass** runs for first authoring, for a page that has never been
saved, and for a slot newly added to the schema — the three cases where a
question sequence genuinely helps because the operator does not yet know what
the page wants.

**Repeating groups on a phone are solvable, because the data already carries
the affordance.** There are 13 repeating groups across the English pages; the
heaviest is `home.focus_areas` at 3 items times 6 fields, 18 inputs, with
`experience.professional_sections` close behind at 3 times 5 plus nested
`detail_groups`. Every one of those groups already has a human-readable
identifier field — `label`, `title` or `eyebrow`. The declaration names it
(`itemLabel`), and each item collapses into a `<details>` whose summary is that
value. The operator sees three named rows, not eighteen inputs, and opens one.
`<details>` is a platform primitive, which is the repo's stated preference.

**The two locales are edited one at a time, and the save enforces parity.**
Side by side does not fit a phone; sequential editing risks exactly the drift
that is live in `fr/experience.md` today. So: edit one locale, and on save run
the shape comparison against the other and block on a difference, naming the
field. This is the runtime replacement for the review-time checklist, and it
converts the wizard's biggest risk into the feature's main guarantee.

**Markdown body prose is not a page problem at all.** Zero of the 16 page files
have a Markdown body; all page content is frontmatter. Long prose exists only
in publications (840 lines across the journal, 186 across case studies). Mobile
editing of long prose is therefore out of scope, and publications keep a plain
body field.

**The preview is the real page.** For a site whose value is how it looks, an
in-editor mock is worse than nothing. The preview is the actual route rendered
at a draft URL.

**What should not be built, or built second.** The copy-tree editor is second —
after the mechanism has proved itself on pages, and once the 5 function entries
have a message-format answer. The `SiteController` editorial ternaries move
into the copy tree _first_, ahead of all of this, because they are a parity
hole today regardless of which approach wins.

## Section 5 — What "agnostic" has to mean to be verifiable

The blunt test as proposed — grep for the owner's name and for any sentence a
visitor could read — is half achievable and half meaningless, and the halves
should be separated.

**The name half is achievable now.** It returns 23 hits across 8 files, in four
kinds that need four different fixes:

- **Settings defaults (10):** the five entries in `config/site.php`, the three
  `?? 'Ismael Rodmacq'` fallbacks in `Seo.php`, the portrait alt in
  `SiteSettingsService.php`, and the email fallback in
  `ContactSubmissionController.php`. The settings already exist; only the
  default value changes. Cheap.
- **Component and copy literals (5):** `AppHeader.vue`, `AppFooter.vue`, and
  the portrait alt text in `copy/{en,fr}/pages/contact.ts`. `site.name` is
  already shared through Inertia. Cheapest of all.
- **CV filename coupling (6):** `ExportStaticPreviewCommand` and
  `SiteController` build paths from `ismael-rodmacq-cv-{locale}.pdf`. Not a
  string swap — it needs the CV to become a settings-addressed asset.
- **Export manifest literals (2):** `name` and `short_name` written into the
  static preview manifest. They should read from settings like everything else.

**The sentence half is meaningless as stated,** because the copy tree _is_
visitor sentences and legitimately lives in the repository. Grepping for
readable sentences would return the whole thing and prove nothing. The test
only becomes real once "declared content source" is a category the code
recognises — which is what this feature builds. The verifiable form: **every
visitor-facing string originates from a declared source** (frontmatter, the
copy tree, or settings), enforced by a lint that fails on editorial literals in
controllers and templates. Achievable, and the same mechanism as the schema.

## Main changes

1. `app/Content/Schema/` — the declaration mechanism, field types, and the 8
   page plus 2 publication declarations.
2. `PageContentRepository` and `ContentRepository` — validate against the
   declaration instead of the flat required-key lists; reverse precedence.
3. `database/seeders/ContentFoundationSeeder` — seed pages and publications
   from Markdown, idempotently.
4. `resources/js/pages/Admin/Pages/Edit.vue` — schema-generated form replacing
   `AdminStructuredValueEditor`.
5. `SiteController` — editorial ternaries out, copy tree in.
6. `resources/content/pages/*/experience.md` — the live YAML defect, plus
   `translation_key` across publications.

## Sequence

Each step is shippable on its own.

| Step | Ships                                                 | Depends on |
| ---- | ----------------------------------------------------- | ---------- |
| 0    | The `/fr/projects` defect is gone                     | —          |
| 1    | Name-agnostic pass; controller prose in the copy tree | —          |
| 2    | Publication schema + CI validation                    | —          |
| 3    | Page schemas + locale parity test                     | 2          |
| 4    | Seeding from Markdown, fidelity proven                | 3          |
| 5    | Precedence flipped; admin edits go live               | 4          |
| 6    | Generated form replaces the JSON tree                 | 3, 5       |
| 7    | Guided pass and draft preview                         | 6          |

Steps 0 to 3 change nothing a visitor sees, and step 3 alone would have caught
the defect fixed in step 0. Step 5 is the one that needs care, and it arrives
with a fidelity test, a revert path and a parity check already in place.

## Docs and tracking sync

- Specs updated: `spec.md`, `plan.md`, `tasks.md`
- `docs/architecture/configurability-inventory.md` — record the decision and
  correct the "respected everywhere" finding
- `docs/architecture/sanity-content-strategy.md` — the content model moved
- A decision record under `docs/architecture/decisions/` for the precedence
  reversal
- The `i18n-content-parity` skill — its prose checklist becomes a test

## Validation

- `php artisan test`
- `composer run lint:check`
- `npm run check`
- `npm run build:ssr`
- `migrate:fresh --seed` followed by the fidelity comparison
