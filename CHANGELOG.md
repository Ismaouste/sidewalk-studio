# Changelog

## [Unreleased]

### Added

- Filed the career chronology as rows. Every position held is now one row per language in `experience_entries`, paired across the two by a `translation_key`, ordered by `started_on` rather than by its index in an array. The period used to be prose inside the same string as the role — `Développeur e-commerce — 2023-2026` was one field, and `Projects.vue` recovered the halves in the browser with `eyebrow.split(/\s+[—–-]\s+/)`, so a role containing a spaced dash would have been read as a date. `date_label` overrides the display where the history is honestly imprecise, because two of the four entries say `Avant 2023` and that is not a range with a missing half; a row with no start date sorts last rather than first, since a null floating to the top would put the vaguest entry above the current job. Seeding splits the eyebrow into a role and a label rather than into dates — guessing one would put an invented fact in a CV — so the page renders identically and a test compares the rebuilt sections against the file-backed payload as encoded JSON, key order included.
- Added `/admin/experience`, which lists the chronology in its own order and edits it. The form is generated from `ExperienceSchemas::entry()` by the same `SchemaField` the page editor uses. Every write touches both languages: creating files the pair under one key and removing removes the pair, because a half-deleted position would leave the site saying different things about the same career depending on which flag the reader clicked. The form says which of the dates and the label is currently winning, since an operator filling in a start date while a label is still set would see nothing change and have no way to know why.
- Added a questionnaire of four short declared questions, whose answers become the marginal notes beside the chronology. `EditorialSpread` has carried that slot since it was written — an italic display quote over a micro-typographic caption — and nothing had ever filled it; the declared schema would in fact refuse a `marginalia` key in the page payload, because it is not declared there. The caption is the question that produced the answer, which is what turns a pull quote into a Q&A. Unanswered is the resting state: with nothing answered the public page is byte for byte what it was, and unanswered questions are skipped rather than left as holes. An empty answer deletes its row rather than storing a blank, so no read has to know the difference between an absent row and a present empty string.
- Opened `/admin` on a dashboard of what is unfinished, replacing a redirect to Settings — the screen an operator needs least often, answering no question they arrived with. Each item names a number, a place to go, and what the public site does about it meanwhile, which is what separates a dashboard from a row of badges. Everything is derived and nothing cached, every count guarded, so a deployment with no database renders an honest empty dashboard. Its unfilled-slot count independently reproduces the figures the browser pass found: `experience` 8 per language, `contact` 1.
- Declared the content model. Every page key and publication type now states its fields in `app/Content/Schema/` — the type of each value, whether it is required, whether it repeats, and which child field names an item inside a repeating group. One declaration drives four things that had no single source before: the save path validates against it, the seeder knows what to seed from it, `/admin` generates its form from it, and the cross-locale parity check compares two payloads through it. Undeclared keys are rejected rather than ignored, because a misspelt key would otherwise read as a missing one and send the operator looking in the wrong place.
- Made the database authoritative for pages and publications, with Markdown as the seed format and the revert path. An edit saved from `/admin` now changes the public page, which is what the admin was built to do. Which source wins is `config('site.content_source')`, so the reversal is one default and rolling it back is an environment variable rather than a release; either source falls back to the other for what it does not hold, so a deployment with no database serves the Markdown exactly as before.
- Replaced the page editor's JSON tree with a form generated from the declaration. Repeating groups collapse into one `<details>` per item, summarised by the field the declaration names — seventeen named rows on the experience record rather than a hundred and twenty-five inputs at once. Prose fields grow to fit with `field-sizing: content`, and every control is 44px at the narrow breakpoint.
- Added a preview that renders the real route from a saved draft, an operator-facing revert to the Markdown seed, and a notice listing declared fields that no content fills — which is what happens when a developer adds a slot to a page written before it existed.
- Added `translation_key` to every publication, pairing it with its other-language self. Six of eleven journal slugs and two of four case studies differ between languages, and nothing in the data linked them: each locale was a directory, and the directory was the link.
- Added `SiteIsAgnosticTest`, which reads the owner's identity out of the settings rather than spelling it, and fails on that identity appearing anywhere under `app/`, `config/`, `routes/`, `resources/js` or `resources/views`. The CV is now addressed by a setting, and the name the browser saves it under is built from the identity at request time.

### Changed

- Moved the stack a full major generation forward: Laravel 12 to 13, Inertia 2 to 3 on both sides, Vite 7 to 8 (Rolldown replaces Rollup), ESLint 9 to 10, PHPUnit 11 to 13, and Wayfinder 0.1.11 to 0.1.21. TypeScript stays on 5.9: TypeScript 7 is the native Go compiler and no longer exposes the JavaScript compiler API that `vue-tsc` and `typescript-eslint` both drive.
- Removed Tailwind, which shipped in every build without a single utility class being used. Base element normalisation is now owned by `resources/css/base.css` inside `@layer reset`. The main stylesheet dropped from 65.2 kB to 27.3 kB and the client build from 3.1s to 1.6s.
- Retuned the `Sunset Signal` dark theme from a warm palette that read brown to electric violet and magenta on deep aubergine glass, with one cyan accent. No green and no amber remain in that theme, and blurred surfaces now saturate above 1 instead of below it.
- Moved every bilingual UI string out of components into `resources/js/copy/<locale>/<group>/`, where each French module is checked against its English counterpart at compile time and keys are kept sorted by lint.
- Wired the lint, format and Pint gates into CI, which previously ran only type checks, the build and the test suite. CI now also runs a PHP 8.4 / 8.5 matrix so the version Vercel serves is exercised.
- Moved `laravel/tinker` to `require-dev` and build-only npm packages to `devDependencies`.
- Rebuilt the mobile navigation sheet and the accessibility panel on the `popover` attribute, so opening, closing, light dismiss, Escape and the top layer come from the browser. Neither needs a z-index or an outside-click listener any more, and the panel's full-screen scrim element is now a `::backdrop`. Above its breakpoint the navigation panel keeps the attribute and simply renders as the tab row, because the UA's `display: none` for a closed popover is outranked by one author media query.
- Moved the active navigation entry to `PublicLocale::navigation()`. The client no longer reimplements locale-prefix and section-prefix matching that the routing table already performs.
- Replaced the breadcrumb's scroll listener, `requestAnimationFrame`, `getBoundingClientRect` and `getComputedStyle` with a `view-timeline` on a one-pixel sentinel, inset by the header height, so the stuck state is reported off the main thread.
- Handed page transitions back to Inertia 3, which wraps the page swap itself. The hand-rolled version started the transition on `start` and held the old frame for the entire request, which is what its 2.2s safety timer existed for. Of the composable's five exports only one had a consumer, and its two custom events had no listeners.
- Stopped CI from naming database, cache, session and queue settings at job level. A job-level `env` becomes a real environment variable and outranks `phpunit.xml`, so the test step no longer runs against different infrastructure from the one developers use.
- Turned the ambient grid to a single axis. The plane rotates through 88 degrees on scroll, so a second axis of lines crossed the first at every angle in between, and the majors — the heaviest lines on the page — were what carried that moire over the text. The horizontals and both major gradients are gone, and `--sw-grid-line-major` with them. The line weight itself barely moved: measured on the hero at 1440, a column peaks 6.8 luma above its backdrop against 11 before, because the noise had been structural rather than chromatic. An earlier attempt that also dropped the weight to a quarter measured 3.6 and put the line under the threshold of a mid-range display.
- Softened the resting border across the site, not only on cards: 11% to 6% in morning, 0.22 to 0.13 in sunset. `--sw-card-hover-border` does not derive from `--sw-border` in either theme, so it is retuned by hand to hold the roughly 2x ratio the resting edge had. `html[data-contrast='boost']` is untouched and now finally reads as a boost in sunset, where it had been measuring weaker than the default it exists to strengthen.

### Fixed

- The "Accessibilité" and "Réglages vie privée" buttons in the footer could not be clicked, on every page and in both languages. The accessibility panel is a `[popover]`, and its base rule declared `display: grid`. The UA sheet gives every closed popover `display: none`, but that rule is UA-origin, so any author declaration beats it — the closed panel kept a **304×212** box over the footer, invisible because its entry transition starts at `opacity: 0`, and swallowed every click inside its rectangle. Both buttons were inside it. Nothing looked broken, which is why it survived: the panel opened correctly when the trigger was reached by keyboard or by script, and only pointer hit-testing revealed it. `.nav-tabs__panel` carried the same declaration and the same fault, leaving a **373×190** dead zone under the header on every page below 960px — where it was worse, because it covered page content rather than two footer controls. Both now declare `display` only on `:popover-open`; the desktop block that turns the nav sheet into the tab row is untouched, and still beats the UA on purpose. `ClosedPopoversTakeNoSpaceTest` pins the rule for every popover in the tree.
- Four of the sixteen page/locale pairs could not be saved from `/admin` at all, including both halves of `experience` — the heaviest record on the site and the one the generated editor was designed around. Opening it and pressing Save, changing nothing, was refused with `[associative_note_widget.eyebrow] should be a line, got null`. Laravel's default global middleware stack contains `ConvertEmptyStringsToNull`, which is written nowhere in `bootstrap/app.php` and which walks nested arrays: `experience` carries eight empty strings across its two widget groups and `contact` one, and each arrived as a null. The declaration is right that a required line is a string and the database stored `''` correctly all along; only the round trip lost it, which is why a suite calling the repository directly stayed green. The conversion is now skipped for the admin routes that post a document rather than form fields, scoped by path because global middleware runs before the router and `routeIs()` is always false there. The metadata columns are untouched: `PageContentRepository::savePage` writes them through `?:`, so an empty title still lands as null. `AdminEditsReachThePublicSiteTest` now saves every page in both locales without editing it and asserts both that the save is accepted and that the stored payload is unchanged — the previous tests all used `colophon`, which carries no empty string and never noticed.
- The admin navigation panel could not be read to the bottom. `align-self: start` sizes it to its content, which is 894px tall against a 900px viewport, and `position: sticky` pins it below the header — so its last 84px sat under the fold with no way to reach them, because the page scroll moves the main column and a sticky element does not travel with it. Nothing was clipped, so nothing looked broken. The panel now caps itself against `100dvh` and scrolls its own overflow; the gutter subtracted is the body's `padding-block` rather than the header offset alone, because before the first scroll the panel has not stuck yet and sits lower, which left it 9px past the fold. The column was also too narrow to read at a flat `220px` and is now `clamp(240px, 19vw, 300px)`, paid for by widening `--sw-admin-shell-max-width` from `1180px` to `1320px` rather than by taking the width from the editor: on a 1440px screen the panel goes 220px to 271px and the content column 527px to 616px.
- The site ignored the operating system's reduced-motion and contrast settings for any visitor who had never touched its own switches. `useAccessibilityPreferences` resolved motion by testing for a single value — `stored === 'reduced' ? 'reduced' : 'full'` — which reads `null` and a deliberate opt-out as the same thing, so `data-motion="full"` was written to everyone who had only ever set the preference at the system level, and the accessibility panel reported motion as on to people whose system said the opposite. Four rules key on that attribute with no `prefers-reduced-motion` query beside them to rescue them, one of them in JavaScript where no media query can reach: the footer's scroll-to-top, which chose `behavior: 'smooth'`. Both preferences are now resolved in the document head alongside the theme, because a composable running at hydration sets the attribute after the first paint — after the animations it exists to prevent have started. An explicit stored choice still wins in both directions.
- The primary button failed WCAG 1.4.3 in the light theme, at **2.95:1** on 14px/500 text — 13px in its small size — and it is the most prominent button on the site. `--sw-button-primary-text` was `var(--sw-text-inverse)` in both themes; inverse means the opposite of the theme's own text, which assumes the button ground follows the theme's lightness. Sunset's light lilac does and measures 7.70:1, but morning's ground is a mid-tone orange, so the same rule put near-white on a mid value. Morning now puts ink on the accent, untouched, at 5.71:1 — untouched because the same hex drives `--sw-ambient-flare`, and darkening the ground to rescue one label would have dimmed the whole theme's atmosphere. Hover had a second failure nothing had caught: at 92% coral it deepened to a value measuring 4.20:1 under near-white and 4.01:1 under ink, failing under both at once, so it now lifts toward the page ground instead and keeps the coral shift at 5.36:1.
- The French experience page printed a JSON blob at its readers. An unquoted YAML scalar containing a colon-space resolves to a single-key mapping rather than a string, and `EditorialSpread.vue` declares `paragraphs: string[]`, so Vue serialised the object into the body copy of `/fr/projects`. The English file used an em dash in the same position and was unaffected. A declared type now rejects it, and a regression test reconstructs the exact line.
- The publication sort stopped at the publication date, so three journal entries sharing 2026-03-08 were ordered by whatever order the source enumerated them in — readdir order for files, primary-key order for rows. The listing order of a published journal should not depend on how a filesystem returns a directory.
- `/colophon` answered 404 without a locale prefix, alone among the eight public pages, and was consequently missing from the static export — which fetches unprefixed paths — so the exported preview 404'd on a page every footer links to.
- The admin page editor had never mounted: `structuredClone` throws `DataCloneError` on Inertia's reactive props.
- The admin showed a different page from the public one, merging the Markdown over the database row, so an operator who saved an edit was shown the file's version back in the form.
- Six `aria-label`s on the public surface were English in both locales, so a French screen-reader user heard "Related items", "Content metadata", "Breadcrumb", "Next step" and "Color theme" in the middle of a French page.
- `/projects` announced a job title in its `Person` schema that the rest of the site had stopped using, and the test pinned the stale copy.
- The French navigation table carried a `/local` label that no entry could match.
- The boost-contrast accessibility mode was unreachable: its composable ignored its own argument and always wrote `default`, while the panel advertised the control as "Soon". Its tokens were already fully authored.
- The static preview export silently stopped rewriting URLs under Inertia 3, which moves the page payload from a `data-page` attribute into a `<script type="application/json">` element.
- `startViewTransition` rejections were never caught, surfacing an `InvalidStateError` in the console on interrupted navigations.
- Anchor links landed underneath the sticky header for want of `scroll-padding-top`.
- The sticky breadcrumb never actually stuck. `overflow-x: hidden` was set on `body` as well as on `html`, and a non-`visible` value on one axis makes the other compute to `auto`, which turns `body` into a scroll container — a sticky descendant then resolves against its scrollport instead of the viewport. Measured before the fix: at a scroll offset of 1500 the bar sat at −1340, well off screen, while the JavaScript kept applying its blur to it.
- The site rendered a blank page for any visitor whose browser refuses web storage. Reading `window.localStorage` throws rather than returning null when site data is blocked, and the anti-flash theme script in `app.blade.php` read it unguarded before anything else ran; `useTheme`, `SiteLayout` and `staticPreview` had the same unguarded reads. All storage access now goes through `resources/js/lib/safeStorage.ts`.
- CI had been failing on `main`, for a configuration reason rather than a code one: the workflow declared `DB_DATABASE`, so the suite ran against a file database that the first `RefreshDatabase` case had already emptied, while `phpunit.xml` asks for an in-memory one. The two affected tests guarded their seed with `Schema::hasTable`, which cannot distinguish a missing table from a present and empty one.
- Removed a focus-ring suppression on the accessibility panel's close button, whose replacement background computed to roughly 3% alpha in the light theme.

### Removed

- `.codex-tmp/`, a full 11 MB duplicate of the project including `vendor/` and `public/build/`, committed by accident and holding stale copies of every source file.
- Dead dependencies with no importer: `@vueuse/core`, `clsx`, `tailwind-merge`, `class-variance-authority`, and the `lib/utils.ts` helper that was their only consumer.
- `docs/ai/blockers.md`, whose only entry (GitHub CLI unreachable) is no longer true, and `docs/career/CODEX_NEXT_PROMPT.md`, a superseded handoff.

### Added

- Local memory (`specs/015-local-memory`): the journal marks entries published since a reader's last visit, and an article offers a partly-read position back. Both live entirely in that reader's browser — no cookie is set, no request carries them, and nothing is recorded server-side, so clearing site data returns the site to its first-visit state. A first-ever reader is shown nothing, since everything being new is indistinguishable from nothing being new, and the comparison point is frozen for the visit so the marks survive a reload. The resume invitation never scrolls on its own; accepting it jumps under a view transition.
- `--sw-scrim` and `--sw-scrim-backdrop-filter` per theme, mixed from `--sw-bg-base` rather than from black, so a scrim darkens the theme instead of draining it. A black scrim over the dark theme's aubergine flattens its violet wash to neutral.
- Hover prefetching on primary navigation and content links, and `content-visibility: auto` on below-the-fold sections.
- Repo-owned Vercel preview runtime support through `api/index.php`, `vercel.json`, and `.vercelignore` for more faithful Laravel previews than the static export alone.
- Architecture and tracking docs for the supported Vercel preview workflow, including its temp-storage bootstrap behavior and local CLI deployment constraints.
- Clarified that GitHub Pages remains the static approximation while Vercel preview is the runtime-oriented preview path.

## [0.2.0] - 2026-03-08

### Added

- Public static preview export for GitHub Pages, keeping the portfolio shell, theme system, loader, and front-end interactions available without the Laravel runtime.
- Seeded site settings snapshot and portable admin bootstrap so the public profile data can be recreated consistently on another machine.
- Contact submission persistence with a lightweight back-office inbox and a public data-processing page wired into the footer.
- Reusable publication widgets, content visuals, placeholder generation, draft support, and richer writing/case-study scaffolding for notes, journal entries, and references.
- Refined public editorial surface across French and English pages, including updated CV sources and regenerated career PDFs.

### Changed

- Reworked the public navigation around `Hello`, `Expériences`, `Journal`, and `Contact`, with a denser mobile menu, sticky breadcrumb behavior, and a footer-driven language/theme/consent control area.
- Consolidated the professional narrative around a single `Expériences` surface, clarified the Jewely / Flippad trajectory, separated associative work, and made public writing and notes serve the portfolio instead of feeling like detached archives.
- Replaced several placeholder sections with profile-specific copy focused on product data, ecommerce delivery, CMS/connectors, tracking, consent orchestration, structured data, and editorial systems.
- Polished the visual system with a calmer `Morning` accent, a less Halloween-like `Sunset` palette, a persistent ambient background, a lighter transition overlay, and a more stable loader treatment.
- Tightened the header, footer, cards, dividers, and content rhythm across desktop and mobile to reduce visual noise and keep the reading flow closer to the final public intent.

### Validation

- `php artisan test`
- `npm run types:check`
- `npm run build`
- `php artisan route:list`

### Deferred

- Footer accessibility controls to disable ambient/background motion, reduce theme-transition effects, and offer additional visual-accessibility modes such as low-contrast / color-vision-friendly rendering.
- Route migration from `?lang=` to dedicated locale paths such as `/fr/...` and `/en/...`.
- Dedicated front-end audit tooling for ongoing Core Web Vitals, cache, and request-performance tracking beyond the current manual optimization pass.

## [0.1.0] - 2026-03-07

### Added

- Laravel 12 + Inertia + Vue 3 + TypeScript application foundation for the public portfolio shell.
- Token-based `Morning Grid` / `Sunset Signal` theme system and reusable public design-system primitives.
- Public editorial pages for `Home`, `Experience`, `Local`, `Projects`, `Writing`, `Case Studies`, `Contact`, and `Labs`.
- Markdown-driven writing and case-study publishing with validated frontmatter and stable routes.
- Consent orchestration for `necessary`, `analytics`, and `media`, with analytics kept on the `none` driver.
- SEO foundations including canonical metadata, JSON-LD, `robots.txt`, and `sitemap.xml`.
- `site_settings` read-side foundation for non-secret public site values, seeded defaults, and shared Inertia/SEO reads.
- Spec-driven docs, roadmap, AI workflow references, and repo-local skills.

### Changed

- Normalized repository metadata and ignore rules for the Windows local-first workflow.
- Aligned the PHP content contract with the documented `updated_at` requirement.
- Refined the homepage into a clearer landing page and moved detailed professional/civic context into `Experience` and `Local`.
- Kept the SSR entrypoint ready without making SSR a required local runtime.
- Prepared the repo for manual Linear and GitHub Project completion without claiming live external integration.

### Deferred

- Additional motion polish and later visual-system refinement on top of the shipped theme/design-system base.
- Richer editorial case-study depth beyond the current public structure.
- CI/CD and deployment automation.
- Any server database migration; PostgreSQL is the preferred first option once a concrete product need exists.
- Real analytics adapters and external tooling integrations.
