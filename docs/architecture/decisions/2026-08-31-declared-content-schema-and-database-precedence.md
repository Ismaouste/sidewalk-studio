# Declared Content Schema and Database Precedence

## Status

Accepted

## Context

The content model was typed, respected in most places, and declared nowhere.
The last of those three had a cost that only became visible when it produced
a defect on the live site.

`resources/content/pages/fr/experience.md` held an unquoted YAML scalar
containing a colon-space. YAML resolves that to a single-key mapping rather
than a string; `EditorialSpread.vue` declares `paragraphs: string[]`; Vue's
`toDisplayString` serialised the object; and a JSON blob rendered in the body
copy of `/fr/projects`. The English file used an em dash in the same position
and was unaffected.

Nothing caught it. `PageContentRepository` validated two of the forty-four
keys a page can hold — `seo_title` and `seo_description`, 4.5% — and spread
the rest into an unread payload. The review-time checklist in the
`i18n-content-parity` skill did not catch it either, because a person
comparing two Markdown files for shape does not evaluate YAML scalar
resolution rules.

Two further problems were structural rather than accidental:

- `Admin/Pages/Edit.vue` handed the payload to a tree editor whose own
  comment called it "arbitrary nested JSON". It was a tree editor because
  there was no schema to build a form from.
- Both repositories preferred Markdown over the database, deliberately, with
  two tests pinning the direction — so `/admin` had been saving page and
  publication edits that the public site ignored.

## Decision

**Declare the content model in code, as data, and make the database
authoritative once seeded from Markdown.**

Three sub-decisions carried the weight:

1. **Layouts stay in code; slots become declared.** Across the eight public
   pages there are 44 distinct top-level keys. Three are shared by all pages;
   the other 41 are each used by exactly one. There is no block vocabulary in
   this content because the pages are not instances of a template — they are
   eight bespoke editorial layouts. A block-builder approach would have had to
   invent that vocabulary and re-express eight unique layouts in it, and its
   finished state is a site that has lost the thing it was selling.

2. **The unit of declaration is the page key**, not the route and not the
   component. `/projects` renders from two page records merged, and
   `/experience` is a 301 to it. Neither a route nor a component could
   describe that page.

3. **Publications were never part of that question.** All 22 journal entries
   share one shape with 15 fields, every one present in every file. They were
   already rows. What they lacked was a `translation_key`: six of eleven
   journal slugs and two of four case studies differ between languages, and
   nothing in the data linked a translation to its original, because each
   locale was a directory and the directory _was_ the link. Rows in one table
   have no directory, so the field landed before the precedence reversal
   rather than with it.

## Consequences

- Positive:
  A declared `string[]` rejects the defect that opened this work,
  mechanically, at save time and in CI. A regression test reconstructs the
  exact line and asserts the message it produces.
- Positive:
  An edit saved from `/admin` changes the public page, which is what the
  admin was built to do.
- Positive:
  The editor is generated from the same declaration the save path validates
  against, so a form cannot offer a field the server will reject.
- Positive:
  Cross-locale parity became a runtime check that runs on every save and in
  CI, replacing a review-time checklist without dropping the guarantee.
- Negative:
  **An operator cannot invent a new _kind_ of section without a developer.**
  A testimonials band on `/home` means someone edits the declaration and adds
  a template branch. This is the cost of decision 1, and it is paid
  deliberately: the layouts are the portfolio, and a block builder makes
  every page capable of looking like every other page. The mitigation is that
  the declaration is data, so adding a slot is an entry plus a branch, and
  the editor regenerates itself.
- Negative:
  Adding a page is now a two-part change — the Markdown file and its
  declaration — and a test fails if only one arrives. That is the intent
  rather than a friction to smooth over.
- Neutral:
  Which source wins is `config('site.content_source')`, so the reversal is
  reviewable on its own and reversible with an environment variable rather
  than a release. Either source falls back to the other for what it does not
  hold, so a deployment with no database — the Vercel one — is unaffected.

## What the verification found that the reasoning had not

Every item here came from running something rather than thinking about it,
and each is fixed in the branch:

- The publication sort stopped at the date, so three entries sharing
  2026-03-08 were ordered by whatever order the source enumerated them in.
  Found by rendering the same routes from both sources and comparing.
- The seeder stopped seeding the moment the database won: it read whichever
  source was authoritative and wrote it straight back, reporting success and
  seeding nothing.
- `/colophon` answered 404 without a locale prefix, alone among the eight
  public pages, and so could not be exported at all.
- The page editor had never mounted: `structuredClone` on Inertia's reactive
  props throws.
- A refused save told the operator nothing, because Laravel reflashes the
  whole request input alongside validation errors and a page pushes a
  cookie-backed session past 4KB.

## References

- Spec: `specs/016-declared-content-schema/spec.md`
- Plan: `specs/016-declared-content-schema/plan.md`
- Declarations: `app/Content/Schema/`
- Related docs: `docs/architecture/content-system.md`,
  `docs/architecture/configurability-inventory.md`
