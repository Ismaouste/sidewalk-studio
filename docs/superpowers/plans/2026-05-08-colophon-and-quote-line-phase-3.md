# Phase 3 — Colophon Route + Loader-Quote Footer Line

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Surface the existing public-infrastructure layer (constitution, build journal, privacy stance, stack) at `/{locale}/colophon` as a discreet finder route, and elevate the existing loader-quote pool into a single typographic line at the bottom of every public page.

**Architecture:** New Markdown content files at `resources/content/pages/{fr,en}/colophon.md` driving a new `SiteController::colophon()` action and a single-column `Colophon.vue` page. A new shared Inertia prop `site.colophonQuote` selects one weighted quote per render via the existing `LoaderQuoteService` (or its current accessor) and `AppFooter.vue` renders it as a small italic line. Both deliverables consume Phase 1 substrate (View Transitions, twilight tokens, vibrant accents) and Phase 2 primitives (`ManifestoOpener`, `SectionIntro`).

**Tech Stack:** Laravel 12 (controller + Inertia shared props + PHPUnit) + Inertia + Vue 3 + Tailwind v4 + existing markdown content pipeline. No new npm dependencies. No new design tokens.

**Out of scope (with rationale):** the locale-switcher Popover rewrite from the spec — the existing two-button inline switcher is a better UX for two locales than a popover dropdown would be (one click vs two, no menu open/close ceremony). The dossier content type — no curated dossier exists yet; the schema work without curated content is busywork. The sparkle-ethos breath — too subjective to land without a visual review pass.

---

## Verification approach (adapted TDD)

Backend: a Pest test covers the `/fr/colophon` and `/en/colophon` routes plus the shared `site.colophonQuote` prop shape. Frontend: `npm run types:check` and `npm run build` after each batch, manual two-theme inspection at the end. Subagent passes for `i18n-parity-reviewer` (new `colophon.md` slugs) and `design-conformance-reviewer` (new Vue file + footer changes) close out Task 6.

Per `CLAUDE.md`: do not run the full Pest suite or `vue-tsc` after every step. Run them at phase checkpoints.

---

## File structure

### Created

- `resources/content/pages/fr/colophon.md` — French colophon body and frontmatter (eyebrow / title / summary, plus structured sections for constitution principles, build-journal pointers, stack note, privacy stance).
- `resources/content/pages/en/colophon.md` — same structure in English (parity-safe).
- `resources/js/pages/Colophon.vue` — single-column page consuming `ManifestoOpener` for the hero and `Panel` rows for the structured sections.

### Modified

- `routes/web.php` — add `Route::get('/colophon', [SiteController::class, 'colophon'])->name('colophon')` inside the locale prefix group.
- `app/Http/Controllers/SiteController.php` — add the `colophon()` method following the same `pages->get('colophon')` pattern as the other Markdown-backed pages.
- `app/Http/Middleware/HandleInertiaRequests.php` — add `colophonQuote` to the shared `site` prop, selected once per request from the existing loader-quote service.
- `resources/js/types/site.ts` — extend `SiteProps` with the `colophonQuote: { text: string; author: string | null } | null` prop.
- `resources/js/components/layout/AppFooter.vue` — add a discreet text link to `/colophon` in the existing `app-footer__links` group, and render the colophon quote line above the legal row.
- `tests/Feature/PublicPagesTest.php` — extend the `test_public_pages_are_reachable` data with `/en/colophon` (and analogous), plus a new test asserting the shared `colophonQuote` prop is exposed.

---

## Task 1: Read foundations

Read-only. No commits.

- [ ] **Step 1: Inspect existing pieces**

Read in full:

- `app/Http/Middleware/HandleInertiaRequests.php` — to know exactly where shared `site` props are assembled and what shape `SiteProps` already has.
- The loader-quote storage: search for `loader_quote`, `LoaderQuote`, or `quotes` in `app/`. Read the model (`app/Models/LoaderQuote.php` if present) and any service/repository that selects a quote for the front-end. Note the field names (likely `body`, `author`, `locale`, `theme`, `weight`).
- `resources/js/types/site.ts` — to find the existing `SiteProps` type definition.
- `app/Http/Controllers/SiteController.php` — pick one Markdown-backed action like `dataProcessing()` to follow as a template for the new `colophon()` method.
- `resources/js/pages/DataProcessing.vue` — pick a reference for a single-column markdown-backed page.

- [ ] **Step 2: Note the loader-quote selection signature**

Write down: which class+method returns one random weighted quote for a given locale (and optionally theme). The Phase 3 backend will call it once per request inside `HandleInertiaRequests::share`.

---

## Task 2: Colophon content (FR + EN)

**Files:**

- Create: `resources/content/pages/fr/colophon.md`
- Create: `resources/content/pages/en/colophon.md`

- [ ] **Step 1: Write `resources/content/pages/fr/colophon.md`**

```yaml
---
seo_title: Colophon — comment ce site est construit
seo_description: Comment Sidewalk Studio est construit, pensé et tenu. Constitution, build journal, stack, vie privée et hébergement.
hero:
    eyebrow: Colophon
    title: 'Comment ce site est construit.'
    summary: 'Un site personnel tenu comme une infrastructure publique. Une constitution, un build journal, une stack documentée, et un cadre vie-privée explicite.'
sections:
    - title: Constitution
      eyebrow: Cadre
      summary: 'Neuf principes numérotés qui tiennent les choix techniques et éditoriaux. Lisibilité avant cleverness, respect du visiteur, écriture longue avant boutons.'
      cta_label: Lire la constitution
      cta_href: https://github.com/Ismaouste/sidewalk-studio/blob/main/.specify/memory/constitution.md
    - title: Build journal
      eyebrow: Pratique
      summary: 'Un journal public de bootstrap, content system, consent orchestration, SEO foundation et site settings, miroir du vault Obsidian privé.'
      cta_label: Parcourir les notes de build
      cta_href: https://github.com/Ismaouste/sidewalk-studio/tree/main/docs/ai/obsidian/build-journal
    - title: Stack et hébergement
      eyebrow: Implémentation
      summary: "Laravel 12, Inertia, Vue 3, Tailwind v4, Vite. SQLite en local, déploiement Vercel. Aucune dépendance JavaScript d'animation. Tokens CSS comme seule surface publique de design."
      cta_label: Voir le code source
      cta_href: https://github.com/Ismaouste/sidewalk-studio
    - title: Vie privée
      eyebrow: Posture
      summary: 'Le formulaire de contact ne stocke pas de donnée. Les analytics restent en opt-in explicite. Les cookies tiers sont gérés via Vanilla Cookie Consent.'
      cta_label: Voir la page traitement
      cta_href: /data-processing
closing:
    eyebrow: Source
    title: 'Le code, les specs et les notes.'
    summary: "Tout est public. Le code source du site sert aussi de référence Laravel utilisable comme point de départ pour d'autres projets."
    cta_label: Ouvrir le repo GitHub
    cta_href: https://github.com/Ismaouste/sidewalk-studio
---
```

- [ ] **Step 2: Write `resources/content/pages/en/colophon.md`**

```yaml
---
seo_title: Colophon — how this site is built
seo_description: How Sidewalk Studio is built, considered, and held. Constitution, build journal, stack, privacy stance, and hosting.
hero:
    eyebrow: Colophon
    title: 'How this site is built.'
    summary: 'A personal site held as public infrastructure. A constitution, a build journal, a documented stack, and an explicit privacy frame.'
sections:
    - title: Constitution
      eyebrow: Frame
      summary: 'Nine numbered principles holding the technical and editorial choices. Readability over cleverness, respect for the visitor, long-form writing before buttons.'
      cta_label: Read the constitution
      cta_href: https://github.com/Ismaouste/sidewalk-studio/blob/main/.specify/memory/constitution.md
    - title: Build journal
      eyebrow: Practice
      summary: 'A public journal covering bootstrap, content system, consent orchestration, SEO foundation, and site settings — mirrored from a private Obsidian vault.'
      cta_label: Browse build notes
      cta_href: https://github.com/Ismaouste/sidewalk-studio/tree/main/docs/ai/obsidian/build-journal
    - title: Stack and hosting
      eyebrow: Implementation
      summary: 'Laravel 12, Inertia, Vue 3, Tailwind v4, Vite. SQLite locally, Vercel for deployment. No JavaScript animation libraries. CSS tokens as the only public design surface.'
      cta_label: Open the source code
      cta_href: https://github.com/Ismaouste/sidewalk-studio
    - title: Privacy
      eyebrow: Stance
      summary: 'The contact form does not store data. Analytics stays explicit opt-in. Third-party cookies go through Vanilla Cookie Consent.'
      cta_label: Open the data-processing page
      cta_href: /data-processing
closing:
    eyebrow: Source
    title: 'The code, the specs, the notes.'
    summary: 'Everything is public. The source doubles as a Laravel reference implementation that can be reused as a starting point for other projects.'
    cta_label: Open the GitHub repo
    cta_href: https://github.com/Ismaouste/sidewalk-studio
---
```

- [ ] **Step 3: Run `i18n-parity-reviewer` subagent on `resources/content/pages/`**

Expected: 8 slugs aligned, 0 with drift, 0 single-locale.

- [ ] **Step 4: Commit**

```bash
git add resources/content/pages/fr/colophon.md resources/content/pages/en/colophon.md
git commit -m "$(cat <<'EOF'
Add colophon page content (FR + EN)

A discreet \"how this site is built\" page surfacing the
constitution, build journal, stack, hosting, privacy stance, and
the public source. Symmetric frontmatter shape across both
locales.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 3: Backend route + controller + shared quote prop + Pest

**Files:**

- Modify: `routes/web.php`
- Modify: `app/Http/Controllers/SiteController.php`
- Modify: `app/Http/Middleware/HandleInertiaRequests.php`
- Modify: `resources/js/types/site.ts`
- Modify: `tests/Feature/PublicPagesTest.php`

- [ ] **Step 1: Add the colophon route**

Inside the `Route::prefix('{locale}')` group in `routes/web.php`, alongside the other site-controller routes:

```php
Route::get('/colophon', [SiteController::class, 'colophon'])->name('colophon');
```

Place it next to the existing `data-processing` route for grouping consistency.

- [ ] **Step 2: Add the `colophon()` action**

In `app/Http/Controllers/SiteController.php`, follow the existing `dataProcessing()` template. Concrete shape (adjust SEO helper wiring to whatever the existing private helpers are called — read those during Task 1):

```php
public function colophon(): Response
{
    $page = $this->pages->get('colophon');
    $seo = Seo::page(
        $page['seo_title'],
        $page['seo_description'],
        '/colophon',
        $this->pageSeoOptions($page, [
            'breadcrumb' => [
                ['name' => 'Home', 'path' => '/'],
                ['name' => 'Colophon', 'path' => '/colophon'],
            ],
        ]),
    );

    return Inertia::render('Colophon', [
        'seo' => $seo,
        'hero' => $page['hero'],
        'sections' => $page['sections'],
        'closing' => $page['closing'],
    ]);
}
```

- [ ] **Step 3: Expose `colophonQuote` in shared site props**

In `app/Http/Middleware/HandleInertiaRequests.php`, locate the `share()` method that builds the `site` prop. Add a `colophonQuote` field selected once per request via the existing loader-quote service. The exact wiring depends on the service signature read in Task 1; the structural pattern is:

```php
'site' => array_merge(
    $existingSiteProps,
    [
        'colophonQuote' => $this->buildColophonQuote($locale),
    ],
),

// elsewhere in the class:
private function buildColophonQuote(string $locale): ?array
{
    $quote = $this->loaderQuotes->randomFor($locale);
    if (! $quote) {
        return null;
    }
    return [
        'text' => $quote->body,
        'author' => $quote->author,
    ];
}
```

If the existing service exposes a method with a different name, use that. Field names (`body` vs `text`, `author` vs `attribution`) likewise follow the existing model.

- [ ] **Step 4: Extend the `SiteProps` type**

In `resources/js/types/site.ts`, locate the `SiteProps` type. Add:

```ts
colophonQuote: {
    text: string;
    author: string | null;
} | null;
```

- [ ] **Step 5: Add Pest coverage**

In `tests/Feature/PublicPagesTest.php`, extend the `test_public_pages_are_reachable` page table to include `/en/colophon` and `/fr/colophon`. Add a new test asserting the shared prop:

```php
public function test_colophon_route_renders_with_shared_quote_prop(): void
{
    $this->get('/fr/colophon')
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('Colophon')
            ->has('hero.title')
            ->has('sections', 4)
            ->has('site.colophonQuote', fn (Assert $quote) => $quote
                ->whereType('text', 'string')
                ->whereType('author', ['string', 'null'])
            )
        );
}
```

- [ ] **Step 6: Run the new tests**

```bash
php artisan test --filter "colophon"
```

Expected: PASS for both the route table extension and the new shared-quote test.

- [ ] **Step 7: Type-check**

```bash
npm run types:check
```

Expected: no errors. (If `Colophon.vue` doesn't exist yet, vue-tsc may complain about Inertia route resolution — acceptable, fixed in Task 4.)

- [ ] **Step 8: Commit**

```bash
git add routes/web.php app/Http/Controllers/SiteController.php app/Http/Middleware/HandleInertiaRequests.php resources/js/types/site.ts tests/Feature/PublicPagesTest.php
git commit -m "$(cat <<'EOF'
Wire colophon route and shared loader-quote prop

Adds the /{locale}/colophon route and SiteController::colophon
action backed by the new Markdown content. Shares a single
loader-quote per request as site.colophonQuote so the footer can
render a discreet typographic line. PHPUnit covers both the new
route and the shared prop shape.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 4: `Colophon.vue` page

**Files:**

- Create: `resources/js/pages/Colophon.vue`

- [ ] **Step 1: Write the page**

```vue
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ManifestoOpener from '@/components/experience/ManifestoOpener.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload, SiteProps } from '@/types';

type ColophonSection = {
    title: string;
    eyebrow: string;
    summary: string;
    cta_label: string;
    cta_href: string;
};

const page = usePage<{ site: SiteProps }>();

const props = defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    sections: ColophonSection[];
    closing: ColophonSection;
}>();

const isExternal = (href: string) => /^https?:\/\//.test(href);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="colophon-page">
            <ManifestoOpener
                :eyebrow="props.hero.eyebrow"
                :thesis="props.hero.title"
                :summary="props.hero.summary"
            />

            <div class="colophon-page__sections">
                <Panel
                    v-for="section in props.sections"
                    :key="section.title"
                    class="colophon-page__row"
                    tone="surface"
                >
                    <p class="type-eyebrow colophon-page__eyebrow">
                        {{ section.eyebrow }}
                    </p>
                    <h2 class="type-h2 colophon-page__title">
                        {{ section.title }}
                    </h2>
                    <p class="type-body colophon-page__summary">
                        {{ section.summary }}
                    </p>
                    <div class="colophon-page__action">
                        <Button
                            :href="section.cta_href"
                            :external="isExternal(section.cta_href)"
                            variant="ghost"
                            arrow
                        >
                            {{ section.cta_label }}
                        </Button>
                    </div>
                </Panel>
            </div>

            <Panel class="colophon-page__closing" tone="grid">
                <p class="type-eyebrow">{{ props.closing.eyebrow }}</p>
                <h2 class="type-h1 colophon-page__closing-title">
                    {{ props.closing.title }}
                </h2>
                <p class="type-body">{{ props.closing.summary }}</p>
                <div class="colophon-page__action">
                    <Button
                        :href="props.closing.cta_href"
                        :external="isExternal(props.closing.cta_href)"
                        variant="primary"
                        arrow
                    >
                        {{ props.closing.cta_label }}
                    </Button>
                </div>
            </Panel>
        </section>
    </SiteLayout>
</template>

<style scoped>
.colophon-page {
    display: grid;
    gap: var(--sw-space-md);
    min-width: 0;
    max-width: 64rem;
}

.colophon-page__sections {
    display: grid;
    gap: var(--sw-space-sm);
    min-width: 0;
}

.colophon-page__row {
    display: grid;
    gap: var(--sw-space-3xs);
    padding: clamp(20px, 2.4vw, 28px);
}

.colophon-page__eyebrow {
    color: var(--sw-accent-violet);
}

.colophon-page__title,
.colophon-page__closing-title {
    margin: 0;
    color: var(--sw-text-primary);
}

.colophon-page__summary {
    margin: 0;
    color: var(--sw-text-secondary);
    text-wrap: pretty;
    max-width: 56rem;
}

.colophon-page__action {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
    padding-top: var(--sw-space-3xs);
}

.colophon-page__closing {
    display: grid;
    gap: var(--sw-space-sm);
    padding: clamp(24px, 3.2vw, 36px);
    margin-block-start: clamp(8px, 1.6vw, 16px);
    background: color-mix(
        in srgb,
        var(--sw-bg-surface) 88%,
        var(--sw-twilight-anchor) 12%
    );
    border-color: color-mix(
        in srgb,
        var(--sw-border) 64%,
        var(--sw-accent-violet) 36%
    );
}

@media (max-width: 640px) {
    .colophon-page {
        gap: var(--sw-space-sm);
    }

    .colophon-page__row {
        padding: var(--sw-space-sm);
    }
}
</style>
```

- [ ] **Step 2: Type-check**

```bash
npm run types:check
```

Expected: no errors.

- [ ] **Step 3: Build**

```bash
npm run build
```

Expected: build succeeds; new `Colophon-*.js` chunk visible in the output.

- [ ] **Step 4: Commit**

```bash
git add resources/js/pages/Colophon.vue
git commit -m "$(cat <<'EOF'
Add Colophon page

Single-column page consuming ManifestoOpener for the hero, a stack
of Panel rows for the four content sections (constitution, build
journal, stack, privacy), and a closing Panel with the source-code
CTA. Reuses the Phase 1 twilight tokens for the closing tint.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 5: Footer link + loader-quote line

**Files:**

- Modify: `resources/js/components/layout/AppFooter.vue`

- [ ] **Step 1: Add the colophon link inside `app-footer__links`**

Inside the existing links group (which contains LinkedIn, Direct contact, Data processing, Back to top), add a new link to the colophon between the data-processing link and the back-to-top button:

```vue
<a class="app-footer__link" :href="colophonHref">
    {{ copy.colophonLabel }}
</a>
```

Add the new `colophonHref` computed and `colophonLabel` copy keys:

```ts
const colophonHref = computed(() =>
    localizePublicHref('/colophon', page.props.site.locale),
);
```

In the `copy` computed, add:

```ts
// fr branch
colophonLabel: 'Colophon',
// en branch
colophonLabel: 'Colophon',
```

(Identical in both locales — "colophon" is one of the rare typographic terms that doesn't translate.)

- [ ] **Step 2: Render the loader-quote line above `app-footer__legal`**

After the closing `</div>` of `app-footer__content` and before `<div class="app-footer__legal">`, add:

```vue
<p v-if="page.props.site.colophonQuote" class="type-meta app-footer__quote">
    <span class="app-footer__quote-text">
        « {{ page.props.site.colophonQuote.text }} »
    </span>
    <span
        v-if="page.props.site.colophonQuote.author"
        class="app-footer__quote-author"
    >
        — {{ page.props.site.colophonQuote.author }}
    </span>
</p>
```

- [ ] **Step 3: Add scoped styles for the quote line**

Append inside the existing `<style scoped>` block, before the responsive blocks:

```css
.app-footer__quote {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    align-items: baseline;
    margin: 0;
    padding: var(--sw-space-3xs) 0;
    color: color-mix(
        in srgb,
        var(--sw-text-muted) 80%,
        var(--sw-text-secondary)
    );
    font-style: italic;
    text-wrap: pretty;
    max-width: 56rem;
}

.app-footer__quote-text {
    font-family: var(--sw-font-display);
    font-size: 0.84rem;
    line-height: 1.45;
    color: color-mix(
        in srgb,
        var(--sw-text-secondary) 78%,
        var(--sw-text-primary)
    );
}

.app-footer__quote-author {
    font-style: normal;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}
```

- [ ] **Step 4: Type-check + build**

```bash
npm run types:check
npm run build
```

Expected: both pass.

- [ ] **Step 5: Commit**

```bash
git add resources/js/components/layout/AppFooter.vue
git commit -m "$(cat <<'EOF'
Add footer link to /colophon and a loader-quote colophon line

A discreet text link to the new colophon route lives next to data
processing and back-to-top. The shared site.colophonQuote prop
renders as an italic typographic line above the legal row, framed
with French quotation marks and the author rendered in upper-case
type-meta.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 6: Verification + push

**Files:**

- Create: `docs/superpowers/plans/2026-05-08-colophon-and-quote-line-phase-3-results.md`

- [ ] **Step 1: Final type and build check**

```bash
npm run types:check
npm run build
```

Both must pass.

- [ ] **Step 2: Run the full backend suite**

```bash
php artisan test --parallel
```

Expected: all tests pass, including the new colophon route and shared-quote assertions.

- [ ] **Step 3: Subagent passes**

Dispatch in parallel:

- `i18n-parity-reviewer` on `resources/content/pages/` — expected: 8 slugs aligned, 0 drift, 0 single-locale.
- `design-conformance-reviewer` on:
    - `resources/js/pages/Colophon.vue`
    - `resources/js/components/layout/AppFooter.vue`

Expected: zero HIGH findings. Note any MEDIUM that warrants follow-up.

- [ ] **Step 4: Manual two-theme inspection**

Load `/fr/colophon` and `/en/colophon` in both `morning` and `sunset` themes. Verify:

- ManifestoOpener renders the title balanced (text-wrap), with twilight backdrop.
- Four section panels stack with eyebrow / title / summary / ghost-button CTA.
- Closing panel uses the twilight tint and primary CTA.
- Footer shows the new "Colophon" link in the actions row.
- Footer quote line renders an italic literary line above the legal row.

- [ ] **Step 5: Lighthouse on `/fr/colophon`**

```bash
npm run audit:lighthouse
```

Expected: Performance score equal to or better than the Phase 2 baseline (no regression from the new page introduction).

- [ ] **Step 6: Write the verification record**

Create `docs/superpowers/plans/2026-05-08-colophon-and-quote-line-phase-3-results.md` with the structure used by the prior phases (commits in scope, tasks executed, verification outcomes, subagent passes, outstanding items, verdict).

- [ ] **Step 7: Commit + push**

```bash
git add docs/superpowers/plans/2026-05-08-colophon-and-quote-line-phase-3-results.md
git commit -m "$(cat <<'EOF'
Record Phase 3 verification results

Captures Phase 3 commits, verification outcomes, subagent passes,
the locale-switcher and dossier scope skips with rationale, and
the verdict before the push.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
git push origin main
```

The Vercel auto-deploy now triggers from the `main` branch update.
