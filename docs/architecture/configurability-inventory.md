# Configurability Inventory

What still has to become data before this repository is a genuinely reusable
Laravel reference implementation, and the current site is one seed of it rather
than the thing itself.

Written 2026-08-31 as the input to a dedicated session. Nothing here is a
decision yet: it is the map, with the hard parts named.

## The target

Two properties, and the second is the harder one:

1. **A complete editorial back-office.** An operator can change anything a
   visitor sees without touching the repository or triggering a build.
2. **An identity-agnostic codebase.** A fresh `migrate --seed` produces a
   neutral, working site. Running the Sidewalk seed on top produces exactly
   what is live today. No name, no phrase, and no route slug is compiled into
   the application.

The test for (2) is blunt: grep the codebase for the owner's name and for any
sentence a visitor could read. Today both return hits in compiled code.

## The approach question, and the finding that bears on it

Two candidate shapes, to be decided by the audit that
`docs/ai/AUDIT_PROMPT_EDITORIAL.md` opens — not here:

- **A, content-managed pages.** Pages become rows and the components become
  block renderers, WordPress-style.
- **B, template with declared slots.** Layouts stay code, because for a design
  portfolio the layouts are the product. What becomes editable is the set of
  slots each page declares, and the editor is generated from those
  declarations.

One finding weighs on that choice more than any other. **The content model is
already typed, and the type is already respected everywhere — but it is
declared nowhere.**

`resources/content/pages/en/home.md` carries `hero {eyebrow,title,summary}`,
`hero_panel []`, `focus_areas [{label,title,summary,href,cta,tone}]`,
`local_teaser {title,summary,points[]}`, `contact_cta {title,summary}`. Every
page has a shape like that, and every French file mirrors its English
counterpart exactly.

That shape lives in three disconnected places:

1. the Markdown files themselves;
2. a flat required-fields list in `PageContentRepository` and
   `ContentRepository`, which only throws on a missing top-level key;
3. a prose checklist in the `i18n-content-parity` skill, enforced at review
   time by a human or an agent.

Nothing in the codebase can enumerate the fields of a page. That is exactly why
`Admin/Pages/Edit.vue` hands the content to `AdminStructuredValueEditor` — a
raw JSON tree editor whose own comment calls the payload "arbitrary nested
JSON". It is a tree editor because there is no schema to build a form from.

Declaring that schema once would give, from a single source: code-level parity
validation replacing the review-time checklist, a generated guided editor,
a definition of what to seed, and the site's shape stated independently of its
content — which is the agnostic goal restated. Whether that argues decisively
for B is the audit's call, not this document's.

## Already data — nothing to do

These are done and should not be reopened:

- Site identity, contact details, social links, SEO defaults, consent copy,
  feature toggles, publishing state, admin state — `SiteSettings` value
  objects behind `/admin/settings`.
- Theme values and branding assets — `/admin/theme`, `/admin/branding`, with a
  rebuild-required flag already modelled.
- Loader quotes — `/admin/loader-quotes`.
- Page payloads and publications — `/admin/pages`, `/admin/publications`, with
  an audit log behind them.
- Laravel language files — `/admin/language-files`.

## Still compiled in

Ordered by how much each one blocks the target.

### 1. The UI copy tree — the largest single block

`resources/js/copy/<locale>/<group>/<domain>.ts` holds every interface string:
navigation actions, page headings, button labels, empty states, the
accessibility panel, the local-memory badge and resume invitation. It is
TypeScript, compiled into the bundle, and its whole safety story is
compile-time: each French module ends in `satisfies typeof import(...)`, which
is what makes a missing key a build error rather than a runtime blank.

That safety is real and worth keeping, which is what makes this hard. Moving
the copy into the database trades a compile-time guarantee for a runtime one.
The question to answer in the dedicated session is not _how_ to move it but
_what replaces the guarantee_ — a seeded shape check, a validation layer that
refuses to save a locale with missing keys, or a hybrid where the tree stays
the fallback and the database only overrides.

Roughly 18 modules across two locales.

### 2. Shell copy and navigation, hardcoded in PHP

- `PublicLocale::shellCopy()` returns two hardcoded arrays of French and
  English strings: header tagline, nav labels, footer note, privacy controls
  label.
- `PublicLocale::navigation()` holds a hardcoded French label map, and reads
  its entries from `config('site.navigation')`.
- `config/site.php` also carries `author`, `labs`, and `contact`.

Config is not editable by an operator, and a hardcoded French map inside a
method is not editable by anyone but a developer. Both belong in settings.

### 3. Names still written into components

- `AppHeader.vue`: `const brandName = 'Ismaël Rodmacq'`
- `AppFooter.vue`: `name: 'Ismaël Rodmacq'` in the footer signature
- `copy/{en,fr}/pages/contact.ts`: the portrait alt text names the person

`site.name` already exists in settings and is already shared through Inertia.
These three are the cheapest wins in the whole document.

### 4. Markdown wins over the database, by design

`resources/content/{pages,writing,case-studies}` are repository files, and the
repositories deliberately prefer them over database rows — there are tests
pinning exactly that (`repository_prefers_markdown_over_hybrid_database_records`,
`it_prefers_markdown_over_database_overrides_for_public_pages`).

So the admin can already edit publications and pages, and the public site
ignores those edits wherever a Markdown file exists. That is not a bug; it was
the right call while the repo was the source of truth. It is, however, the
single thing that makes the back-office feel incomplete, and reversing the
precedence is a content-model decision rather than a UI one.

Decide in the session: does Markdown become the seed format only, with the
database authoritative afterwards? That is the version that satisfies both
goals at once — the current site ships as Markdown seeds, and editing happens
in the database.

### 5. Routes and slugs

`/journal`, `/projects`, `/local`, `/contact`, `/case-studies`, `/sparkle`,
`/colophon` are route literals, with a legacy-redirect array beside them.
A site for someone else has different sections. This is the deepest change in
the list and probably the last one worth doing — possibly never, if the answer
is that the reference implementation ships with these sections and a fork
renames them.

### 6. Supported locales

`PublicLocale::supported()` returns `['en', 'fr']`. Everything downstream —
the copy tree shape check, the content directories, the switcher — assumes
exactly two. Making the set configurable is not hard; making the _parity
guarantee_ work for N locales is the same problem as (1).

### 7. Design tokens

`resources/css/tokens.css` is a build-time stylesheet. `/admin/theme` already
writes theme settings and raises a rebuild-required flag, so the shape of the
answer exists. What is missing is the last mile: which token families an
operator may set, and whether they are injected as inline custom properties on
the document (no rebuild) or written back into the stylesheet (rebuild).

Note that a runtime-editable palette interacts with the contrast work: values
an operator picks freely can fail the ratios the design docs commit to. Any
editor here needs a contrast check on save, not just a colour picker.

### 8. Career assets

CV PDFs and the illustrated portrait are static files under `public/`. Small,
but they are the most obviously personal artefacts in the repository.

## Suggested order for the dedicated session

1. Names in components (§3) — an hour, and it makes the grep test meaningful.
2. Shell copy and navigation into settings (§2) — self-contained, no new
   guarantees needed.
3. Decide the Markdown-versus-database precedence (§4). Everything editorial
   is blocked behind this one decision, so it should be made before any UI is
   built for it.
4. The copy tree (§1), once (3) has set the pattern for how a runtime override
   keeps its shape guarantee.
5. Locales (§6) and tokens (§7) after, since both depend on the answer to (1).
6. Routes (§5) only if the reference implementation is meant to be renamed
   rather than forked.

## Constraints to carry into that session

- Do not trade the compile-time locale-parity guarantee for nothing. If it
  becomes a runtime check, the check has to exist and has to be tested.
- Keep the seed reproducible: `migrate:fresh --seed` must produce the current
  site exactly, or the agnostic goal is unverifiable.
- Nothing here should reach the visitor's browser as new tracking. The
  local-memory features are client-only by design; a configurable site must not
  quietly become a configurable data collector.
