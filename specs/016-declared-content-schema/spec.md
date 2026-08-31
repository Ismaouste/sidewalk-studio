---
linear_issue: TODO
github_project_item: TODO
github_project_status: implemented
obsidian_note: TODO
release: TODO
title: Declared Content Schema
status: implemented
---

# Feature Specification: Declared Content Schema

The site's content model is declared once, in code, as data. Every page and
publication states the fields it holds. That single declaration then produces
four things it cannot produce today: a generated editor, a runtime locale
parity check, a definition of what to seed, and a statement of the site's shape
that is independent of its current content.

## Recommendation

**Approach B — template with declared slots — with the pages keeping their
layouts.** The reasoning is below, and so is what it gives up.

This session was asked to defend a choice between A (pages become rows,
components become block renderers) and B (layouts stay code, slots become
declared). The content itself decides it, and not narrowly:

**Across the eight public pages there are 44 distinct top-level content keys.
Three are shared by all pages — `hero`, `seo_title`, `seo_description`. The
other 41 are each used by exactly one page.** `sparkle_facts` belongs to
`/sparkle`. `stack_groups` and `trajectory` belong to the experience record.
`consent`, `storage` and `controls` belong to `/data-processing`.

There is no shared block vocabulary in this content, because the pages are not
instances of a template. They are eight bespoke editorial layouts. Approach A
would have to invent a block vocabulary the data does not contain, then
re-express eight unique layouts in it. That is not a migration; it is a rewrite
whose finished state is a site that has lost the thing it was selling.

The publications are the mirror image, and they settle the other half of the
question. **All 22 journal entries share exactly one shape, with 15 fields,
every one present in every file.** They are already rows. They need no approach
debate — only the precedence fix in section 3.

So the answer is not "B everywhere". It is: **publications are already
row-shaped and need a precedence flip; pages need a declared per-page schema.**
Applying a single approach to both is the mistake either option would make on
its own.

### What this gives up

An operator cannot invent a new _kind_ of section without a developer. A
testimonials band on `/home` means someone edits the schema and adds a template
branch. Approach A would have allowed that from the admin.

The cost is real and worth paying here, for a reason specific to this
repository: the layouts are the portfolio. A block builder makes every page
capable of looking like every other page, and for a site whose argument is its
craft, that capability is a liability rather than a feature. The mitigation is
that the schema is data, so adding a slot is a schema entry plus a template
branch — a small, reviewable change — not a rebuild of the editor, which
regenerates itself from the declaration.

### The two scope corrections the evidence forces

**The unit of declaration is the page key, not the route and not the
component.** `/experience` 301-redirects to `/projects`, and `Projects.vue` is
rendered from _two_ page records merged: `projects.md` supplies the hero,
`experience.md` supplies the twelve other keys. A schema attached to routes or
to components could not describe that page. It attaches to page keys, and a
route declares which page keys it composes.

**A schema covering only the frontmatter solves roughly 60% of the problem.**
Visitor-facing text lives in four places today, not one:

| Source               | Volume                                   | Parity guarantee |
| -------------------- | ---------------------------------------- | ---------------- |
| Markdown frontmatter | 170 leaf fields across 8 EN pages        | review-time only |
| `resources/js/copy/` | 18 modules per locale, ~136 entries each | compile-time     |
| `SiteController.php` | ~20 editorial strings                    | **none**         |
| Vue templates        | 4 strings on the public surface          | none             |

The third row is the one that had not been counted. `SiteController` carries
inline `app()->getLocale() === 'fr' ? '...' : '...'` pairs holding the journal
intro, the notes intro and the case-studies archive intro — real editorial
prose, in a controller, bypassing the copy tree's compile-time parity guarantee
entirely. The journal and case-studies index pages have no Markdown file at
all; their headline and description _are_ those ternaries.

The fourth row is small but two of its four entries are worse than they look.
`Labs.vue` holds a link label and a card title in English only. The other two
are `aria-label` attributes — `RelatedItemsStrip.vue:20` and
`ContentMetaRow.vue:12` — which a sighted reviewer never sees and a French
screen-reader user hears in English. The constitution calls accessibility
non-negotiable, so these are a defect on their own terms, not just untidiness.

The admin surface carries a further 15 English literals. That is defensible —
the back-office is single-locale by design — but it means the agnostic test has
to distinguish the two surfaces rather than grep the whole tree.

The good news underneath all of it: four public strings is a very short list.
The copy-tree discipline is real. The problem is not indiscipline, it is the
absence of a declaration.

## Problem

### The schema exists, is declared nowhere, and is therefore not respected

The framing handed to this session was that the content model is already typed,
already respected everywhere, and declared nowhere. The first and third are
true. **The second is false, and a live defect on the production site proves
it.**

`resources/content/pages/fr/experience.md:25` holds an unquoted YAML scalar
containing a colon-space:

```yaml
- Côté commerce, j'ai conçu et tenu les connecteurs entre l'ERP, le PIM et
  les catalogues marchand: création produit automatique et orchestration...
```

YAML parses that as a single-key mapping, not a string. `EditorialSpread.vue`
declares `paragraphs: string[]` and renders `{{ paragraph }}`, so Vue's
`toDisplayString` serializes the object and prints a JSON blob into the body
copy of `/fr/projects`. The English file uses an em dash in that position, so
it stays a string. Neighbouring lines 23 and 45 _are_ quoted, which shows the
trap has been hit before and handled by hand each time.

Nothing caught it. `PageContentRepository` validates two fields — `seo_title`
and `seo_description` — then spreads the rest into `payload` unread. **Two of
the 44 keys a page can hold are checked: 4.5%.** The review-time checklist in
the `i18n-content-parity` skill did not catch it either, because a human
comparing two files for shape does not evaluate YAML scalar rules.

That is the argument for this feature in one line: the defect is exactly the
class a declared `string[]` rejects mechanically, at save time and in CI, and
it shipped instead.

### The admin edits raw JSON, because there is nothing to build a form from

`Admin/Pages/Edit.vue` keeps flat inputs for metadata and hands everything else
to `AdminStructuredValueEditor`, whose own comment calls the payload
"arbitrary nested JSON". It is a tree editor because no schema exists to
generate a form from. That is a symptom, not a design choice.

### And those edits never reach the site

`ContentRepository` and `PageContentRepository` deliberately prefer Markdown
over database rows, with two tests pinning it. The admin already saves page and
publication edits that the public site ignores.

## Desired outcome

- One declaration per page key and per publication type, in code, enumerating
  every field with its type, whether it repeats, and which subfield labels an
  item inside a list.
- A save path that refuses content failing its declaration, in both locales,
  with cross-locale parity as a runtime check that exists and is tested —
  replacing the review-time checklist without dropping the guarantee.
- A generated editor: a sectioned form by default, a guided pass for first
  authoring, both driven from the same declaration.
- Markdown as the seed format, the database authoritative after seeding, and
  `migrate:fresh --seed` reproducing the current site exactly.
- An agnostic test that means something, running in CI.

## In scope

- The declaration mechanism, and declarations for 8 page keys and 2
  publication types.
- Runtime shape validation including cross-locale parity, with tests.
- Reversing the Markdown/database precedence, and rewriting the two tests that
  pin the current direction.
- Seeding pages and publications from the current Markdown.
- Replacing `AdminStructuredValueEditor` with a schema-generated editor.
- Moving the `SiteController` editorial ternaries into the copy tree.
- The name-agnostic pass over the 23 identified hits.

## Out of scope

- Moving the copy tree into the database. It keeps its compile-time guarantee
  until the schema mechanism has proved itself on pages, and its 5 function
  entries (pluralization) have no row representation yet.
- Configurable routes and slugs.
- Locales beyond the current two.
- Runtime-editable design tokens.
- Mobile editing of long publication prose.

## Constraints

- English only for code, docs and specs.
- The compile-time locale parity guarantee is not traded for nothing. Where it
  becomes a runtime check, the check exists and is tested.
- `migrate:fresh --seed` reproduces the current site exactly.
- No new visitor-facing tracking. A configurable site must not quietly become a
  configurable data collector.
- Baseline stays green: `npm run check`, `composer run lint:check`,
  `php artisan test` (85 tests, 756 assertions).

## Acceptance criteria

- [x] `fr/experience.md` renders as prose, and a test rejects the mapping form.
- [x] Every page key and publication type has a declaration, and a test asserts
      every Markdown file under `resources/content/` validates against its own.
- [x] A test asserts EN and FR resolve to the same shape for every page key,
      and fails on the drift that is live today.
- [x] Saving a page from `/admin` changes the public page.
- [x] `migrate:fresh --seed` produces a database whose rendered output matches
      the current Markdown-rendered site exactly.
- [x] An operator can revert a page to its seeded state without a developer.
- [x] The admin page editor renders typed inputs, not a JSON tree.
- [x] No editorial string remains in a controller or a Vue template.
- [x] The agnostic test runs in CI and passes with the owner's name present
      only in seeds and settings defaults.

## Tracking

- Linear: keep the primary issue key in `linear_issue:`
- GitHub Project: mirror `github_project_item`, `github_project_status`, and
  `release` in `docs/ai/github-project/roadmap-spec-issue-map.md`
- Obsidian: set `obsidian_note` to the repo mirror path under
  `docs/ai/obsidian/build-journal/`
