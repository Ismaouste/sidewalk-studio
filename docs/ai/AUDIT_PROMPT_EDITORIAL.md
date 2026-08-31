# Audit prompt: editorial featurisation

Paste the block below to open the session. It is written to be handed to an
agent cold, and its job is to **produce a decision and a plan**, not to start
building.

---

## The session's job

Audit this repository and produce a plan for making the public site editable
through a real dashboard, with the site itself becoming identity-agnostic and
the current content becoming seed data.

Two approaches are on the table. **Do not assume either one.** The first thing
this session owes is a defended recommendation, and "neither, here is a third"
is an acceptable answer.

**Approach A — content-managed pages.** Pages become rows. The Vue components
become templates that render arbitrary block structures, WordPress-style. The
current site is recreated as seeded page records.

**Approach B — template with declared slots.** The pages keep their bespoke
layouts, because those layouts _are_ the product for a design portfolio. What
becomes editable is the set of content slots each page declares. The editor is
generated from those declarations: a guided, mobile-first sequence of
questions, prefilled from what the page holds today.

The desired editing experience, in the owner's words, is mobile-first and
question-led — a form that walks you through the content and its metadata,
prefilled from the existing values, with those values remaining the single
source of truth until they are seeded.

## What is already known — do not re-derive this

A previous session produced `docs/architecture/configurability-inventory.md`.
Read it first. It lists what is already data (settings, theme, branding, loader
quotes, pages, publications, language files, all behind `/admin`) and what is
still compiled in.

Four findings from that work bear directly on the decision:

1. **The content model is already typed — but only by convention.** Page
   frontmatter carries a rich nested schema. `resources/content/pages/en/home.md`
   declares `hero {eyebrow,title,summary}`, `hero_panel []`,
   `focus_areas [{label,title,summary,href,cta,tone}]`,
   `local_teaser {title,summary,points[]}`, `contact_cta {title,summary}`.
   Every page has a shape like this and every FR file mirrors its EN
   counterpart exactly.

2. **That schema is declared nowhere.** It exists in three disconnected places:
   the shape of the Markdown files themselves; a flat required-fields list in
   `PageContentRepository` and `ContentRepository` that only throws
   `RuntimeException` on a missing top-level key; and a prose checklist in the
   `i18n-content-parity` skill, enforced at review time by a human or an agent.
   Nothing in the codebase can enumerate the fields of a page.

3. **The admin already edits pages, as raw JSON.** `Admin/Pages/Edit.vue` has
   flat inputs for metadata and hands the nested content to
   `AdminStructuredValueEditor`, whose own comment says the payload is
   "arbitrary nested JSON". It is a tree editor, not a form, precisely because
   there is no schema to build a form from.

4. **Markdown beats the database, deliberately, with tests pinning it.**
   `ContentRepositoryTest::test_repository_prefers_markdown_over_hybrid_database_records`
   and `PageContentRepositoryTest::test_it_prefers_markdown_over_database_overrides_for_public_pages`
   both assert that a database row is ignored when a Markdown file exists. So
   the admin can already save edits the public site will not show.

## The questions to answer

Answer each with evidence from the codebase, not from general CMS knowledge.

### 1. Is the implicit schema complete enough to be declared?

Go through every file in `resources/content/pages/{en,fr}/` and every
publication in `resources/content/{writing,case-studies}/`. Build the union of
field shapes. Report: how many distinct field types are in use, how many pages
share shapes, and how much of it is genuinely irregular. The answer decides
whether a declared schema is a weekend or a month.

Then check the other half: how much page content does **not** come from
frontmatter at all, but from `resources/js/copy/` or from literals inside the
page components? A schema that covers the frontmatter and misses the copy tree
solves half the problem.

### 2. Which approach does the evidence support?

Judge A against B on criteria that matter here, and state them before judging:

- What happens to the FR/EN parity guarantee under each? Today it is a
  compile-time guarantee for the copy tree and a review-time one for content.
  Approach A dissolves both into free-form blocks. Approach B could turn both
  into a single runtime validation. Neither is automatically better — say which
  guarantee is worth what.
- What happens to the site's visual identity? A block builder makes every page
  capable of looking like every other page, which for a portfolio whose value
  is its layouts may be a cost rather than a feature.
- What is the migration path from the current Markdown, and is it reversible?
- Which one lets `migrate:fresh --seed` reproduce the live site exactly?

### 3. Resolve the Markdown-versus-database precedence

This blocks everything editorial and is finding (4) above. The likely answer is
that Markdown becomes the seed format and the database becomes authoritative
afterwards — but check what breaks: the static export path, the content tests,
the FR/EN fallback logic in `ContentRepository`, and whether an operator can
recover from a bad edit without a developer.

Whatever the answer, the two tests pinning the current precedence have to be
rewritten deliberately, not deleted.

### 4. Pressure-test the guided-form editor

The requested UX is a question sequence, mobile-first, prefilled. Audit it as a
design, not just as a feature:

- A wizard is excellent for creating and often miserable for editing. Walking
  twenty questions to fix one typo is worse than a form. What does the design
  do about the difference between first authoring and later correcting?
- How does it handle a repeating group — `focus_areas` has three items today,
  each with six fields — on a phone?
- How do the two locales appear? Side by side does not fit a phone; sequential
  risks drift, which is the exact problem the parity contract exists to
  prevent.
- What does it do about content that is currently a Markdown body rather than a
  field, and what is the mobile editing story for long prose?
- Where does the preview live, given the site's whole value is how it looks?

State clearly if some part of this should not be built, or should be built
second.

### 5. What does "agnostic" have to mean to be verifiable

Propose the test. The blunt version: grep the codebase for the owner's name and
for any sentence a visitor could read, and expect nothing outside seeds. Say
whether that test is achievable, what it would take, and what it would cost.

## Output

Write a spec package under `specs/016-<slug>/` using `.specify/templates/`:
`spec.md`, `plan.md`, `tasks.md`. Follow the repo's Spec Kit conventions.

The spec must open with the recommendation and the reasoning for it, including
what it gives up. The plan must be sequenced so that each step ships something
usable on its own — a plan whose first useful outcome is six weeks away is the
wrong plan for a portfolio site that is already live.

Do not write feature code in this session. If a small spike is needed to answer
a question, say so and keep it out of the main branch.

## Constraints to carry in

- English only for code, docs and specs.
- Do not trade the compile-time locale-parity guarantee for nothing. If it
  becomes a runtime check, the check must exist and must be tested.
- `migrate:fresh --seed` must reproduce the current site exactly, or the
  agnostic goal is unverifiable.
- Nothing here reaches the visitor as new tracking. The local-memory features
  are client-only by design; a configurable site must not quietly become a
  configurable data collector.
- The baseline stays green: `npm run check`, `composer run lint:check`,
  `php artisan test` (85 tests, 756 assertions).
- Toolchain: PHP and Composer are not on the Bash tool's PATH. Prepend
  `/c/Users/ismae/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe`
  and call Composer through `php /c/Users/ismae/.local/bin/composer.phar`.
  `npm run dev` needs PHP on PATH too, for the Wayfinder plugin.
