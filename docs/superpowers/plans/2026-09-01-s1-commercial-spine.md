# S1 Commercial Spine Implementation Plan

> **Status: EXECUTED 2026-09-01** — all seven tasks shipped to main (commits a0fb02f..0c6a419), full baseline green (165 tests, lint, types, build), CI success. Post-deploy smoke on the Vercel URL is the one open step: the Vercel CLI is not logged in on this machine.

**Goal:** Give the site a commercial spine — a `/services` page with a content-managed price grid, Services and Case studies in the primary nav, a reworked Home, contact qualification fields — and flip the repo open source.

**Architecture:** Everything follows the declared-content pattern already in place: a new `services` page key is declared in `PageSchemas`, stored as FR/EN markdown, rendered by a `SiteController` method into a new Inertia page. Navigation stays server-resolved (`config/site.php` + `lang/{locale}/public.php` + `PublicLocale::navigation()`). No new dependencies.

**Tech Stack:** Laravel 13, Inertia 3, Vue 3, hand-authored CSS on `--sw-*` tokens, PHPUnit 13.

**Spec:** `docs/superpowers/specs/2026-09-01-commercial-repositioning-design.md` (§3 IA, §4 offer, §10 open source, §12 resolved decisions)

## Global Constraints

- All code, docs, and content-file keys English-only; public copy bilingual FR/EN in shape parity (`DeclaredPageContentTest` + `LanguageFileParityTest` enforce it).
- Never hardcode colors/fonts/spacing/motion — only `--sw-*` tokens from `resources/css/tokens.css`. Test both `morning` and `sunset` themes. Sunset carries no green and no amber.
- TS copy modules: FR ends with `satisfies typeof import('../../en/...')`, keys sorted (`sort-keys`).
- Commits go straight to `main`; push only after the full baseline (Task 7) because push deploys to Vercel.
- Do not run full builds/tests after every small change — per task test runs only, full baseline once at the end.
- The owner's name may appear only in `lang/{locale}/site.php`, `database/seeders/data/site-settings.json`, `resources/content/`, `docs/`, `tests/` (`SiteIsAgnosticTest`).
- Production (Vercel) ships no database: no feature may require a DB write to work. Contact keeps the mailto/WhatsApp handoff transport.

**Already done — do not redo:** the `/experience`↔`/projects` route swap (`9018e2b`), single title composition (`PageTitleIsComposedOnceTest`), reduced-motion seeding (`AccessibilityPreferencesFollowTheSystemTest`). The `projects`+`experience` page-key merge stays out of S1 (route-level duality is resolved; the key merge is invasive and buys nothing user-visible).

---

### Task 1: The `services` page key — schema, content, route, controller, plumbing

**Files:**

- Modify: `app/Content/Schema/PageSchemas.php` (KEYS, ROUTE_COMPOSITION, new `services()` method)
- Create: `resources/content/pages/en/services.md`, `resources/content/pages/fr/services.md`
- Modify: `routes/web.php` (route + unprefixed-redirect list)
- Modify: `app/Http/Controllers/SiteController.php` (new `services()` method)
- Modify: `app/Support/PublicLocale.php` (`pageKeyForRequest` match arm)
- Modify: `lang/en/public.php`, `lang/fr/public.php` (`breadcrumbs.services`)
- Modify: `app/Http/Controllers/SitemapController.php` (static entries)
- Modify: the export command path list in `app/Console/Commands/` (the array holding `'/experience'`, `'/local'`, ~line 213)
- Create: `resources/js/pages/Services.vue` (minimal shell; full design in Task 2)
- Test: `tests/Feature/PublicPagesTest.php`

**Interfaces:**

- Produces: page key `services` at route name `services`, path `/{locale}/services`; Inertia page `Services` with props `seo, hero, offers, modifiers, engagement, legalNote, contactCta`.
- `DeclaredPageContentTest` auto-validates both locale files against the schema once the key is in `PageSchemas::KEYS` — no test writing needed for shape parity.

- [ ] **Step 1: Failing test** — in `tests/Feature/PublicPagesTest.php::test_public_pages_are_reachable` add `'/en/services' => 'Services · Ismael Rodmacq',` to the `$pages` array, and add `'/services'` to the unprefixed-redirect paths list in `test_every_public_page_redirects_from_its_unprefixed_path`.

- [ ] **Step 2: Run it** — `php artisan test --filter=PublicPagesTest` → FAIL (404).

- [ ] **Step 3: Declare the schema.** In `PageSchemas`: add `'services'` to `KEYS` and `'services' => ['services']` to `ROUTE_COMPOSITION`; add the method:

```php
public static function services(): ContentSchema
{
    return new ContentSchema('services', 'Services', [
        ...self::meta(),
        self::hero(),
        Field::group('offers', [
            Field::line('label', 'Label'),
            Field::line('title', 'Title'),
            Field::text('summary', 'Summary'),
            Field::line('price', 'Price line'),
            Field::line('price_meta', 'Price meta')->optional(),
            Field::text('points', 'Included points')->repeating(),
            Field::line('cta', 'Call to action'),
            Field::line('tone', 'Tone'),
        ], 'Offers')->repeating(itemLabel: 'title'),
        Field::group('modifiers', [
            Field::line('title', 'Title'),
            Field::text('summary', 'Summary'),
        ], 'Modifiers'),
        Field::group('engagement', [
            Field::line('title', 'Title'),
            Field::group('steps', [
                Field::line('title', 'Title'),
                Field::text('summary', 'Summary'),
            ], 'Steps')->repeating(itemLabel: 'title'),
        ], 'Engagement'),
        Field::text('legal_note', 'Legal note'),
        Field::group('contact_cta', [
            Field::line('title', 'Title'),
            Field::text('summary', 'Summary'),
        ], 'Contact call to action'),
    ]);
}
```

and the `'services' => self::services(),` match arm in `for()`.

- [ ] **Step 4: Verify the TVA mention before writing prices.** WebSearch "franchise en base de TVA 2026 seuil micro-entrepreneur prestations de services" — confirm the current threshold and whether "TVA non applicable, art. 293 B du CGI" is the correct mention for the legal_note. Use what the search returns, not memory.

- [ ] **Step 5: Write both content files.** `resources/content/pages/en/services.md` — five offers matching spec §4 exactly (Site local from €2,900 HT; Boutique from €7,500 HT; Signal from €900 HT/month; Direction technique TJM €650 HT 1–3 days/week; Forfait plateforme on quote), modifiers block (associations −30%, cultural institutions adapted quote), engagement steps (first call → scoped proposal → build in the open → measured handover), legal_note from Step 4, contact_cta pointing at `/contact`. `tone` values must come from the set Home already uses (`dominant | green | sun | coral | violet`). Then `fr/services.md` in strict shape parity (same keys, same array lengths), French copy written to the same standard as existing FR pages.

- [ ] **Step 6: Route + redirect + controller + page key.** `routes/web.php` inside the locale group, after `/labs`:

```php
Route::get('/services', [SiteController::class, 'services'])->name('services');
```

Add `'/services'` to the unprefixed `$legacyPath` foreach list. In `PublicLocale::pageKeyForRequest`, add `'services' => 'services',`. In `SiteController`:

```php
public function services(): Response
{
    $page = $this->pages->get('services');
    $seo = Seo::page(
        $page['seo_title'],
        $page['seo_description'],
        '/services',
        $this->pageSeoOptions($page, [
            'breadcrumb' => [
                ['name' => PublicLocale::homeLabel(app()->getLocale()), 'path' => '/'],
                ['name' => PublicCopy::line('breadcrumbs.services'), 'path' => '/services'],
            ],
        ]),
    );

    return Inertia::render('Services', [
        'seo' => $seo,
        'hero' => $page['hero'],
        'offers' => $page['offers'],
        'modifiers' => $page['modifiers'],
        'engagement' => $page['engagement'],
        'legalNote' => $page['legal_note'],
        'contactCta' => $page['contact_cta'],
        'cvDownloads' => $this->cvDownloads(),
    ])->withViewData(['seo' => $seo]);
}
```

- [ ] **Step 7: Lang keys.** `lang/en/public.php` breadcrumbs: `'services' => 'Services',`; `lang/fr/public.php`: `'services' => 'Services',` (same word both locales, like Journal).

- [ ] **Step 8: Sitemap + export.** `SitemapController::staticPageEntries()`: add `['locale' => 'en', 'path' => '/services']` and the `fr` twin. Export command path array: add `'/services'`.

- [ ] **Step 9: Minimal `Services.vue`** so the route renders — `SiteLayout` + `SeoMeta` + hero only, props typed as in Interfaces. Full design is Task 2.

- [ ] **Step 10: Run** — `php artisan test --filter="PublicPagesTest|DeclaredPageContentTest|PageContentRepositoryTest"` → PASS.

- [ ] **Step 11: Commit** — message in repo style, e.g. `Give the offer an address before giving it a face`.

### Task 2: Services.vue — the page itself

**Files:**

- Modify: `resources/js/pages/Services.vue`
- Reference: `resources/js/pages/Home.vue` (focus-area card treatment), `docs/style/components.md`, `docs/style/tokens.md`

**Interfaces:** Consumes Task 1 props verbatim. No new props.

- [ ] **Step 1: Invoke `frontend-design:frontend-design`** before layout work — this is a new page, the flagship commercial surface.
- [ ] **Step 2: Build mobile-first**: hero; offer cards in a responsive grid (one column mobile, price line prominent, `points` inside a native `<details>` per card — platform primitive, first public use on the site); modifiers note as an aside; engagement steps as an ordered editorial list (scroll-driven reveal consistent with `EditorialSpread`); legal note in micro-typography near the prices; contact CTA reusing the Home CTA treatment. Tones map through existing `home-tone--*`-style token classes — no new colors.
- [ ] **Step 3: Check both themes** (`morning`, `sunset`) via `npm run dev` visual pass; sunset must show no green/amber drift on card accents.
- [ ] **Step 4: Dispatch `design-conformance-reviewer`** on Services.vue; fix findings.
- [ ] **Step 5: Commit** — e.g. `Lay the price grid out in daylight and dusk`.

### Task 3: Navigation — Services and Case studies step in, Experience steps back

**Files:**

- Modify: `config/site.php` (navigation array)
- Modify: `lang/en/public.php`, `lang/fr/public.php` (navigation groups)
- Modify: `resources/js/copy/en/layout/navigation.ts`, `resources/js/copy/fr/layout/navigation.ts` (action entries)
- Modify: `resources/js/copy/en/layout/footer.ts`, `resources/js/copy/fr/layout/footer.ts` + `resources/js/components/layout/AppFooter.vue` (experience link joins the footer)
- Test: `tests/Feature/PublicNavigationStateTest.php`

**Interfaces:** Consumes route name `services` from Task 1. Nav order produced: `/ · /services · /case-studies · /journal · /contact`.

- [ ] **Step 1: Failing tests** — add to `PublicNavigationStateTest`:

```php
public function test_services_and_case_studies_are_primary_sections(): void
{
    $this->assertSame(['/services'], $this->activePaths('/en/services'));
    $this->assertSame(['/case-studies'], $this->activePaths('/en/case-studies'));
}

public function test_experience_left_the_menu_but_keeps_its_page(): void
{
    $this->assertSame([], $this->activePaths('/en/experience'));
}
```

Also update `test_the_rule_is_locale_independent`: the `/fr/experience` assertion flips from `['/experience']` to `[]`.

- [ ] **Step 2: Run** — `php artisan test --filter=PublicNavigationStateTest` → FAIL.
- [ ] **Step 3: `config/site.php`** navigation becomes:

```php
'navigation' => [
    ['label' => 'Hello', 'href' => '/'],
    ['label' => 'Services', 'href' => '/services'],
    ['label' => 'Case studies', 'href' => '/case-studies'],
    ['label' => 'Journal', 'href' => '/journal'],
    ['label' => 'Contact ✍🏽', 'href' => '/contact'],
],
```

- [ ] **Step 4: Lang navigation groups.** EN: drop `'/experience'`, add `'/services' => 'Services'`, `'/case-studies' => 'Case studies'`. FR: drop `'/experience'`, add `'/services' => 'Services'`, `'/case-studies' => 'Études de cas'`.
- [ ] **Step 5: Copy action entries** (NavTabs meta line, keyed by path). EN adds `'/case-studies': 'Read the proof'`, `'/services': 'See offers'`; FR adds `'/case-studies': 'Lire les preuves'`, `'/services': 'Voir les offres'` — keys sorted, `satisfies` intact. Leave the now-unused `'/experience'` action keys in place only if removing them breaks the `Reference` type; otherwise remove from both.
- [ ] **Step 6: Footer link.** Add an Experience link beside the colophon one in `AppFooter.vue` (`localizePublicHref('/experience', …)`), label `experienceLabel` added to both footer copy modules (EN `'Experience'`, FR `'Expériences'`).
- [ ] **Step 7: Run** — `php artisan test --filter="PublicNavigationStateTest|PublicPagesTest"` → PASS. Quick `npm run types:check` for the copy modules.
- [ ] **Step 8: Commit** — e.g. `Put the offer and the proof on the menu, move the record to the footer`.

### Task 4: Home rework — content only

**Files:**

- Modify: `resources/content/pages/en/home.md`, `resources/content/pages/fr/home.md`

**Interfaces:** `focus_areas` keeps its declared shape (label/title/summary/href/cta/tone) — no schema or Vue change.

- [ ] **Step 1:** EN `home.md`: first focus card becomes Services (`href: /services`, tone `dominant`, summary naming the three offer lines), second stays Case studies but the summary now names the proof pillars (Atlas Dépannage platform work, crown-dp.com e-commerce CMS, this open-source site), third stays Contact. `hero.summary` gains the commercial framing (offers + technical direction). `contact_cta` mentions the price grid. `seo_description` updated to match.
- [ ] **Step 2:** Mirror in FR with identical shape (same keys, same array lengths).
- [ ] **Step 3:** `php artisan test --filter="DeclaredPageContentTest|PublicPagesTest"` → PASS.
- [ ] **Step 4: Dispatch `i18n-parity-reviewer`** on the two files; fix drift.
- [ ] **Step 5: Commit** — e.g. `Make the front door say what is for sale`.

### Task 5: Contact qualification

**Files:**

- Modify: `app/Content/Schema/PageSchemas.php` (`contact()` form group)
- Modify: `resources/content/pages/en/contact.md`, `resources/content/pages/fr/contact.md`
- Modify: `resources/js/pages/Contact.vue` (three native `<select>`s)
- Modify: `app/Http/Controllers/ContactSubmissionController.php` (validation + mailto body)
- Test: create `tests/Feature/ContactQualificationTest.php`

**Interfaces:** POST `contact.store` accepts optional `project_type`, `budget`, `timeline` (strings, max 120); they ride into the mailto body as labeled lines. Select options live in content frontmatter (`form.project_type_options` etc.) so the admin can edit them.

- [ ] **Step 1: Failing test:**

```php
public function test_qualification_fields_reach_the_message(): void
{
    $response = $this->post('/en/contact', [
        'name' => 'Ada',
        'email' => 'ada@example.com',
        'summary' => 'A message long enough to pass the minimum length rule.',
        'project_type' => 'E-commerce',
        'budget' => '5-10k',
        'timeline' => 'This quarter',
    ]);

    $response->assertRedirect();
    $this->assertStringContainsString(rawurlencode('E-commerce'), $response->headers->get('Location'));
}
```

- [ ] **Step 2: Run** — FAIL (fields ignored, body lacks the value).
- [ ] **Step 3: Schema.** In `contact()`'s form group add:

```php
Field::line('project_type_label', 'Project type label'),
Field::line('project_type_options', 'Project type options')->repeating(),
Field::line('budget_label', 'Budget label'),
Field::line('budget_options', 'Budget options')->repeating(),
Field::line('timeline_label', 'Timeline label'),
Field::line('timeline_options', 'Timeline options')->repeating(),
```

- [ ] **Step 4: Content.** Both locales gain the six keys. EN options — project type: `Technical direction / CTO time`, `E-commerce build`, `Website`, `Campaigns & growth`, `Something else`; budget: `< €3k`, `€3–8k`, `€8–20k`, `> €20k`, `Daily rate`; timeline: `Urgent`, `This quarter`, `Exploring`. FR mirrors shape with translated values.
- [ ] **Step 5: Controller.** Validation adds `'project_type' => ['nullable', 'string', 'max:120']` (same for `budget`, `timeline`); the `$lines` array gains labeled lines for each present value (labels via `PublicCopy::group('contact_mail')` — add `project_type`, `budget`, `timeline` keys to `contact_mail` in both lang files).
- [ ] **Step 6: Vue.** Three native `<select>`s between company and summary, options from `form.project_type_options` etc., styled with existing form tokens; posted with the form.
- [ ] **Step 7: Run** — `php artisan test --filter="ContactQualificationTest|DeclaredPageContentTest|LanguageFileParityTest"` → PASS.
- [ ] **Step 8: Dispatch `design-conformance-reviewer`** (Contact.vue changed) and `i18n-parity-reviewer` (content changed); fix findings.
- [ ] **Step 9: Commit** — e.g. `Let the first message say what kind of project it is`.

### Task 6: Open-source flip

**Files:**

- Create: `LICENSE`, `CONTRIBUTING.md`
- Modify: `README.md`

- [ ] **Step 1: LICENSE** — MIT, copyright `2026 Ismaël Rodmacq`, followed by a clearly separated note: `resources/content/**`, `docs/career/**`, and brand assets are NOT covered — all rights reserved.
- [ ] **Step 2: README** — reposition per spec §10: what the project demonstrates (declared content schema, consent-first marketing, platform primitives, FR/EN parity), who it serves, quickstart, validation baseline, case-study/journal pointers, license note. Keep the existing "reusable reference" framing, add the commercial context.
- [ ] **Step 3: CONTRIBUTING.md** — short: how to run it, the validation baseline, the two-theme rule, content parity rule, that content dirs are not open to PRs.
- [ ] **Step 4: Secret scan** — `winget install gitleaks` (or download release binary), run `gitleaks git .` over full history; also `git log --all --oneline -- .env` must return nothing. Any finding blocks the flip until resolved.
- [ ] **Step 5: Commit** the three files — e.g. `Open the code, keep the words`.
- [ ] **Step 6: Flip** — `gh repo edit Ismaouste/sidewalk-studio --visibility public --accept-visibility-change-consequences`, then verify with `gh repo view --json visibility`.

### Task 7: Baseline and push

- [ ] **Step 1:** `npm run check` (lint + format + types) → clean.
- [ ] **Step 2:** `composer run lint:check` (Pint) → clean (PHP path setup per CLAUDE.md).
- [ ] **Step 3:** `php artisan test` → all green.
- [ ] **Step 4:** `npm run build` → succeeds.
- [ ] **Step 5:** Invoke `superpowers:verification-before-completion`; then `git push` (deploys to Vercel).
- [ ] **Step 6:** Post-deploy smoke: fetch `https://<prod>/en/services` and `/fr/services`, check 200 + title.

## Self-review notes

- Spec coverage: §3 nav (Task 3), `/services` + §4 grid (Tasks 1–2), Home (Task 4), contact qualification (Task 5), §10 + §12.6 open-source flip in S1 (Task 6). Hygiene items verified already-shipped and excluded deliberately. Booking (cal.com) is S3 scope, not here.
- Type consistency: `offers/modifiers/engagement/legal_note/contact_cta` names match between schema (snake_case content keys) and controller props (camelCase only for `legalNote`/`contactCta`, matching the existing `local_teaser`→`localTeaser` convention.
- The `services` page key deliberately reuses the shared `hero`/`meta` factorings and Home's tone vocabulary rather than inventing new primitives.
