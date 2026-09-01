# Resume prompt

Paste the block below to start the next session. It repeats things you already
know, because it is written to be handed to an agent cold.

For the editorial back-office work specifically, use
`docs/ai/AUDIT_PROMPT_EDITORIAL.md` instead — that one opens an audit and ends
in a spec package, not in code.

---

## Where things stand

PR #19 is **merged** into `main` (merge commit `09eb934`, 18 commits) and
Vercel production is deployed. CI is green on the PHP 8.4 / 8.5 matrix — it had
been red on `main` for three runs before that PR.

Shipped there: the stack up a major generation (Laravel 13, Inertia 3, Vite 8
with Rolldown, ESLint 10, PHPUnit 13), Tailwind removed, bilingual UI copy moved
into `resources/js/copy/`, NavTabs and AccessibilityPanel rebuilt on the Popover
API, BreadcrumbTrail on a `view-timeline`, page transitions handed back to
Inertia, and two local-memory features (`specs/015-local-memory`).

PR #20 is **merged** (`52d6fc5` on `main`): the palette rework and the
breadcrumb fixes from `feat/light-blue-and-lit-grid`.

`feat/declared-content-schema` is **merged** (`09a2e41` on `main`, deployed):
`specs/016-declared-content-schema/` built end to end, in eight commits that
each land one shippable step. The content model is declared in
`app/Content/Schema/`, the database is authoritative with Markdown as the seed,
and `/admin` generates its page editor from the declaration. See the changelog
and
`docs/architecture/decisions/2026-08-31-declared-content-schema-and-database-precedence.md`.

## Baseline that must stay green

`npm run check`, `composer run lint:check`, `php artisan test`
(**158 tests / 1718 assertions** on `main`), `npm run build:ssr`. Both themes
get checked in a browser on any visual change.

Because `main` deploys to Vercel on push, that baseline is a pre-push gate now,
not a pre-merge one.

## Toolchain

PHP and Composer are not on the Bash tool's PATH:

```
export PATH="/c/Users/ismae/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe:$PATH"
php /c/Users/ismae/.local/bin/composer.phar <cmd>
```

`npm run dev` needs PHP on PATH too, for the Wayfinder plugin. If
`composer install` returns HTTP 400, that is unauthenticated GitHub rate
limiting — export `COMPOSER_AUTH` from `gh auth token`. Never fall back to
`--prefer-source`: `vlucas/phpdotenv` ships `tests/fixtures/env/nul.env`, and
`nul` is a reserved Windows device name that aborts the whole checkout.

**Never use `perl -i` without a backup suffix on Windows.** It destroys the file
and leaves a randomly-named temporary beside it. It cost `tokens.css` in the
session that wrote this. Use the Edit tool.

## Open work

### 1. ~~Browser pass on the generated page editor~~ — done

Run on `/admin/pages/experience/fr`. The editor itself held up: all 22 declared
top-level keys render, the counts match the content, `SchemaField` recurses
correctly through three levels — repeating group, nested repeating group,
repeating scalar — `pills` renders as both optional and repeating, the item
labels populate each `<summary>`, and both themes are conformant. No console
errors.

**Saving was broken, and had been for every page carrying an empty string.**
An identity save was refused. Laravel's default `ConvertEmptyStringsToNull`
walks nested arrays, so every `''` in a payload arrived as `null`; four of the
sixteen page/locale pairs — `experience` and `contact` in both languages —
could not be saved at all. Fixed in `bootstrap/app.php` and held by
`AdminEditsReachThePublicSiteTest::test_every_page_and_locale_survives_a_save_that_changes_nothing`,
which iterates all sixteen rather than naming one. The previous tests all used
`colophon`, which carries no empty string.

**`SITE_CONTENT_SOURCE=files` is not needed.** The declaration and the database
were both right; the request was wrong. The default stays `database`.

Two things deliberately **not** done on that branch, both stated in the spec:

- The copy tree under `resources/js/copy/` keeps its compile-time guarantee.
  It moves once the mechanism has proved itself on pages, and its five
  function entries (pluralisation) have no row representation yet.
- Publications still use the tree editor at `/admin/publications`; only pages
  have a generated form. Their declaration exists and validates, so the same
  `SchemaField` component can drive them.

### 1b. The editorial back office — shipped, and what it left open

`specs/017-editorial-back-office/` is built: the chronology is rows with real
dates (`experience_entries`), `/admin/experience` edits it in both languages at
once, four declared poetic questions surface as the marginalia beside the
spreads, and `/admin` opens on what is unfinished rather than on Settings.

Left deliberately open:

- **The four questions have no answers yet.** They are the owner's voice, so
  nothing was written into them. Until they are answered the chronology has no
  marginal notes, which is a resting state and not a bug.
- **The seeded positions have no dates**, only the labels they were seeded
  with. Filling `started_on` and clearing `date_label` is what hands the range
  to the record and stops the page needing a January edit.
- **Positions cannot be reordered by hand.** `position` exists as a tie-break
  inside one date and is not exposed, because the dates are meant to do that
  work. If two entries ever need a manual order inside the same month, that is
  the field to surface.
- **Publications still use the tree editor.** Their declaration exists and
  validates, so the same `SchemaField` can drive them.

### 2. The rest of the configurability map

`docs/architecture/configurability-inventory.md` now carries a table of what
shipped and what did not. Untouched: routes and slugs, locales beyond the
current two, and runtime-editable design tokens.

### 3. Two things found and left alone

- **`SESSION_DRIVER=cookie` caps a session at 4KB**, which silently swallowed
  a validation message during this work until the controller stopped
  reflashing the request input. Anything else that flashes a page-sized
  payload will hit the same wall without saying so.
- ~~**The `<title>` carries its suffix twice.**~~ Fixed in `20ae06c`; the site
  is named once and the locale spells it, held by
  `PageTitleIsComposedOnceTest`.

## Known debts, to take when they cross the path

- ~~`useAccessibilityPreferences` never seeds `data-motion` from
  `prefers-reduced-motion`.~~ Fixed in `cbb59a7`, contrast with it, and seeded
  from the document head rather than at hydration — the composable alone would
  have set the attribute after the first paint. Held by
  `AccessibilityPreferencesFollowTheSystemTest`.
- ~~`--sw-button-primary-bg` on `--sw-button-primary-text` measures 2.95:1.~~
  Fixed in `17d6e4f`: morning now puts ink on the untouched orange (5.71:1),
  and hover lifts toward the page ground instead of deepening, which had been
  failing under both text colours at once.
- A link with `prefetch="hover"` and `cache-for` runs **two** full visit cycles
  when a pointer rests on it, so it crossfades twice. A prefetch-policy call.
- `--sw-tab-line` and `--sw-header-bg` have no consumers;
  `html[data-scroll-lock]` in `reset.css` has no writer. `@property
--sw-grid-line-opacity` at `tokens.css:12` is registered and read by nothing
  — it survived the substrate plan that introduced it.
- `actions/checkout@v4` and `actions/setup-node@v4` are forced onto Node 24 —
  move to `@v5`.
- ContentVisual SVGs are served `max-age=3600`, so a palette change takes up to
  an hour to reach returning visitors.
- A refused save shows **one** violation out of however many there are. The
  controller flashes them all under the `payload` key, but Inertia's
  `resolveValidationErrors` returns `$errors[0]` per key unless `withAllErrors`
  is set, and it is `false` by default. `Edit.vue:145` already does
  `if (Array.isArray(value)) return value;` — the front end is written for the
  list it can never receive. The eight `experience` violations reached the
  browser as one, so an operator would have fixed them one save at a time.
  `HandleInertiaRequests` extends that middleware, so the property can be
  overridden there — but it would turn **every** error prop in the app from a
  string into an array, which the other forms are not written for. Override
  `resolveValidationErrors()` for the `payload` key alone.
- The admin shell preloads the four public display faces — `dm-sans` 400/500,
  `fraunces` 400 italic, `syne` 700 — and uses none of them, so every
  back-office page logs four "preloaded but not used" warnings and spends the
  bytes.
- A `public/hot` file can survive a killed `npm run dev` and point at a dead
  Vite server. Laravel did not honour it in the case seen, but it is a live
  landmine for an unstyled admin. Delete it when the dev server is not running.

## Conventions

- English for all code, docs and specs. Conversation can be French.
- Never hardcode colours, fonts, spacing or motion: `--sw-*` tokens.
- `sunset` carries no green and no amber; blurred surfaces saturate above 1.
- No CSS anchor positioning: tried and reverted in `db61a48` because Chromium
  placed popups wrong and never fired `position-try-fallbacks`.
- Commit straight to `main`; no feature branches. Keep the commits atomic so
  `git log` stays readable, and run the full baseline before each push, because
  a push publishes.
- Subagents: `design-conformance-reviewer` after any Vue component or CSS,
  `i18n-parity-reviewer` after anything under `resources/content/pages/`.
