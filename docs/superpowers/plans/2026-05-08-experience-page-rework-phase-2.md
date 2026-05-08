# Experience Page Rework — Phase 2 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the current `/experience` page composition (3-Panel work-grid + linear story articles + widgets) with a five-block editorial layout (ManifestoOpener / SignageStrip / EditorialSpread × N / DataPlate / closing Panel), built on the Phase 1 substrate (View Transitions, scroll-driven CSS, `text-wrap: balance`, vibrant token palette).

**Architecture:** Four new presentational components in `resources/js/components/experience/`, each consuming Sidewalk Studio primitives (`SectionIntro`, `Panel`, `Button`, `LegendChip`) and `--sw-*` tokens. A new `thesis` frontmatter key on both locale `experience.md` files becomes the manifesto opener line. The four professional / associative sections are exposed as a single `signageItems` strip plus one `EditorialSpread` per section. `trajectory`, `strengths`, and `focus_areas` consolidate into a single three-column `DataPlate`. `stack_groups` and `career_snapshot` frontmatter remain (i18n-parity preserved) but are no longer rendered on the page.

**Tech Stack:** Laravel 12 (backend mapping + Pest tests) + Inertia + Vue 3 + Tailwind v4 + Vite. CSS features used: `view-transition-name`, `@starting-style`, `animation-timeline: view()`, `text-wrap: balance` (already substrate), `color-mix(in oklch, ...)` for theme-aware accents.

---

## Verification approach (adapted TDD)

Backend: a new Pest test covers `thesis` flowing through `SiteController::experience()`.
Frontend: `npm run types:check` after type updates, `npm run build` after each batch, manual two-theme inspection at the end. Subagents `i18n-parity-reviewer` and `design-conformance-reviewer` close out Task 9.

Per `CLAUDE.md`: do **not** run the full Pest suite or `vue-tsc` after every step. Run them at the checkpoints called out in each task. The phase verification (Task 9) consolidates the heavy runs.

---

## File structure

### Created

- `resources/js/components/experience/ManifestoOpener.vue` — hero block with `view-transition-name: page-hero`, `@starting-style` entry. Wraps `SectionIntro size="hero"`.
- `resources/js/components/experience/SignageStrip.vue` — sticky-on-desktop wayfinding strip; chips link to spread anchors.
- `resources/js/components/experience/EditorialSpread.vue` — two-column desktop spread (prose + side-rail) with optional `marginalia` figure and scroll-driven intertitle reveal.
- `resources/js/components/experience/DataPlate.vue` — three-column typographic plate consolidating trajectory + strengths + focus areas.

### Modified

- `resources/content/pages/fr/experience.md` — add `thesis: …` under the `hero` block.
- `resources/content/pages/en/experience.md` — add `thesis: …` under the `hero` block.
- `app/Http/Controllers/SiteController.php` — add `'thesis' => $experience['thesis']` to the experience props mapping.
- `tests/Feature/PublicPagesTest.php` — add a feature test asserting `thesis` flows through to Inertia props (or new test file if scoped narrower).
- `resources/js/pages/Projects.vue` — full template rewrite to consume the four new components plus a thinner closing Panel; scoped CSS slimmed accordingly.

### Untouched (frontmatter keys preserved for parity but not rendered in new composition)

- `stack_groups`, `career_snapshot`, `associative_note_widget`, `side_projects_widget`, `cv_downloads`. These remain in `experience.md` so the i18n parity reviewer does not flag drift, and so the Projects.vue prop type remains structurally valid. They simply do not render on the page.

---

## Task 1: Read foundational files for Phase 2

This task is **read-only** — no commits.

**Files:**

- Read: `resources/js/pages/Projects.vue` (full template + scoped style) — already done in handoff session; re-verify if returning fresh.
- Read: `app/Http/Controllers/SiteController.php` lines 130-200 (the experience handler).
- Read: `resources/content/pages/fr/experience.md` and `resources/content/pages/en/experience.md`.
- Read: `resources/js/components/design-system/SectionIntro.vue` to confirm the `size: 'hero'` API.
- Read: `resources/js/types.ts` (or `resources/js/types/index.ts`) to find the existing `ExperienceSection`-style types.
- Read: `tests/Feature/PublicPagesTest.php` to learn the existing Pest pattern for asserting Inertia page props.

- [ ] **Step 1: Read each file fully**

For each path above, use the Read tool. The plan's later tasks reference exact identifiers (e.g., `$experience['thesis']`, `SectionIntro size="hero"`, prop key casing).

- [ ] **Step 2: Note the prop-name mapping convention**

`SiteController::experience()` maps snake_case YAML keys to camelCase Inertia props (e.g., `professional_sections` → `professionalSections`). Use the same convention in Task 3 (the new `thesis` key is already a single word, so it stays `thesis`).

---

## Task 2: Add `thesis` to content (FR + EN)

**Files:**

- Modify: `resources/content/pages/fr/experience.md`
- Modify: `resources/content/pages/en/experience.md`

- [ ] **Step 1: Add `thesis` to `fr/experience.md`**

Insert immediately after the `hero` block (the `summary: …` line in the FR file). The exact insertion:

```yaml
thesis: "Reprendre l'existant, le rendre lisible, et le laisser plus calme qu'à l'arrivée."
```

- [ ] **Step 2: Add `thesis` to `en/experience.md`**

Insert at the matching position:

```yaml
thesis: "Take over what's there, make it readable, and leave it calmer than I found it."
```

- [ ] **Step 3: Run i18n parity reviewer**

Dispatch the project-local `i18n-parity-reviewer` subagent on `resources/content/pages/`. Expected: 7 slugs aligned, 0 with drift, 0 single-locale.

- [ ] **Step 4: Commit**

```bash
git add resources/content/pages/fr/experience.md resources/content/pages/en/experience.md
git commit -m "$(cat <<'EOF'
Add thesis line to experience content (FR + EN)

A single-sentence career thesis used by the new ManifestoOpener
component. Same key in both locales — i18n parity preserved.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 3: Backend pass-through and types

**Files:**

- Modify: `app/Http/Controllers/SiteController.php` — extend the experience handler's mapping array.
- Modify: `tests/Feature/PublicPagesTest.php` — add an assertion for the new prop.
- Modify: `resources/js/pages/Projects.vue` — extend `defineProps` (the rest of the rewrite happens in Task 8; here we only widen the prop type so subsequent compilations work).

- [ ] **Step 1: Extend the controller mapping**

In `app/Http/Controllers/SiteController.php`, locate the `experience()` method's `Inertia::render(...)` call. Add `'thesis' => $experience['thesis']` to the props array. Place it in the mapping near the `'hero' => …` entry to keep related keys grouped.

The exact insertion (relative to the existing `'hero' => ...` line):

```php
'hero' => [
    'eyebrow' => $experience['hero']['eyebrow'],
    'title' => $experience['hero']['title'],
    'summary' => $experience['hero']['summary'],
],
'thesis' => $experience['thesis'],
```

If the existing file structures `'hero'` differently, follow the same pattern: add `'thesis' => $experience['thesis']` immediately after the `hero` block in the props array.

- [ ] **Step 2: Add a Pest test for the new prop**

In `tests/Feature/PublicPagesTest.php`, find an existing test that hits `/fr/experience` and asserts on Inertia page props. Add a new test in the same style. If no exact match exists, add this:

```php
it('exposes the thesis line on the experience page', function () {
    $response = $this->get('/fr/experience');

    $response->assertOk();
    $response->assertInertia(fn (Inertia\Testing\AssertableInertia $page) =>
        $page->component('Projects')
            ->has('thesis')
            ->where('thesis', "Reprendre l'existant, le rendre lisible, et le laisser plus calme qu'à l'arrivée.")
    );
});
```

If the project uses class-based PHPUnit instead of Pest function syntax in this file, adapt to that style — preserve the assertion semantics.

- [ ] **Step 3: Run the new test**

```bash
php artisan test --filter "exposes the thesis line"
```

Expected: PASS.

- [ ] **Step 4: Widen `Projects.vue` props type**

In `resources/js/pages/Projects.vue`, locate the `defineProps<{ … }>()` call. Add `thesis: string;` to the prop type. Do not yet rewrite the template — the rewrite lands in Task 8. This step exists so `npm run types:check` continues to pass on intermediate commits.

- [ ] **Step 5: Type-check**

```bash
npm run types:check
```

Expected: no errors.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/SiteController.php tests/Feature/PublicPagesTest.php resources/js/pages/Projects.vue
git commit -m "$(cat <<'EOF'
Pass thesis frontmatter through to Inertia and widen prop type

Adds 'thesis' to the experience props mapping in SiteController
so the Vue page can read it. Pest covers the contract. The Vue
prop type widens now so build passes ahead of the template
rewrite in a later commit.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 4: Create `ManifestoOpener.vue`

**Files:**

- Create: `resources/js/components/experience/ManifestoOpener.vue`

- [ ] **Step 1: Write the component**

```vue
<script setup lang="ts">
import SectionIntro from '@/components/design-system/SectionIntro.vue';

defineProps<{
    eyebrow: string;
    thesis: string;
    summary: string;
}>();
</script>

<template>
    <div class="manifesto-opener">
        <SectionIntro
            :eyebrow="eyebrow"
            :title="thesis"
            :description="summary"
            size="hero"
        >
            <template #actions>
                <slot name="actions" />
            </template>
        </SectionIntro>
    </div>
</template>

<style scoped>
.manifesto-opener {
    view-transition-name: page-hero;
    contain: layout;
    position: relative;
    padding: var(--sw-space-sm) 0;
    isolation: isolate;
}

.manifesto-opener::before {
    content: '';
    position: absolute;
    inset: 0;
    z-index: -1;
    background: radial-gradient(
        ellipse at 18% 12%,
        color-mix(in oklch, var(--sw-twilight-glow) 40%, transparent),
        transparent 58%
    );
    pointer-events: none;
    border-radius: var(--sw-radius-lg);
}

@supports (view-transition-name: none) {
    @media (prefers-reduced-motion: no-preference) {
        .manifesto-opener {
            opacity: 1;
            transform: translateY(0);
            transition:
                opacity 600ms cubic-bezier(0.22, 1, 0.36, 1),
                transform 600ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        @starting-style {
            .manifesto-opener {
                opacity: 0;
                transform: translateY(8px);
            }
        }
    }
}

@media (prefers-reduced-motion: reduce) {
    .manifesto-opener {
        transition: none;
    }
}
</style>
```

- [ ] **Step 2: Type-check**

```bash
npm run types:check
```

Expected: no errors.

- [ ] **Step 3: Commit**

```bash
git add resources/js/components/experience/ManifestoOpener.vue
git commit -m "$(cat <<'EOF'
Add ManifestoOpener component

Wraps SectionIntro size=hero and adds a view-transition-name on
the outer wrapper so the hero block morphs across Inertia
navigations. Adds a @starting-style entry transition gated on
prefers-reduced-motion.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 5: Create `SignageStrip.vue`

**Files:**

- Create: `resources/js/components/experience/SignageStrip.vue`

- [ ] **Step 1: Write the component**

```vue
<script setup lang="ts">
defineProps<{
    items: Array<{ id: string; eyebrow: string; label: string }>;
    ariaLabel?: string;
}>();
</script>

<template>
    <nav class="signage-strip" :aria-label="ariaLabel ?? 'Section navigation'">
        <a
            v-for="item in items"
            :key="item.id"
            :href="`#${item.id}`"
            class="signage-strip__chip"
        >
            <span class="signage-strip__eyebrow">{{ item.eyebrow }}</span>
            <span class="signage-strip__label">{{ item.label }}</span>
        </a>
    </nav>
</template>

<style scoped>
.signage-strip {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
    padding: var(--sw-space-3xs);
    border: 1px solid color-mix(in srgb, var(--sw-border) 78%, transparent);
    border-radius: var(--sw-radius-lg);
    background: var(--sw-bg-surface);
    -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
    backdrop-filter: var(--sw-surface-backdrop-filter);
    min-width: 0;
}

.signage-strip__chip {
    display: inline-grid;
    gap: 2px;
    padding: var(--sw-space-3xs) var(--sw-space-xs);
    border-radius: var(--sw-radius-md);
    color: var(--sw-text-primary);
    text-decoration: none;
    transition:
        background-color var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.signage-strip__chip:hover {
    background: color-mix(in srgb, var(--sw-accent-violet) 14%, transparent);
    transform: translateY(-1px);
}

.signage-strip__chip:focus-visible {
    outline: 2px solid var(--sw-border-focus);
    outline-offset: 2px;
}

.signage-strip__eyebrow {
    font-family: var(--sw-font-heading);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--sw-accent-violet);
}

.signage-strip__label {
    font-family: var(--sw-font-body);
    font-size: 14px;
    font-weight: 500;
}

@media (prefers-reduced-motion: reduce) {
    .signage-strip__chip {
        transition: none;
    }
    .signage-strip__chip:hover {
        transform: none;
    }
}

@media (min-width: 1040px) {
    .signage-strip {
        position: sticky;
        top: calc(var(--sw-header-offset) + var(--sw-space-xs));
        z-index: var(--sw-z-content);
    }
}

@media (max-width: 640px) {
    .signage-strip {
        overflow-x: auto;
        flex-wrap: nowrap;
        scrollbar-width: thin;
    }
}
</style>
```

- [ ] **Step 2: Type-check**

```bash
npm run types:check
```

Expected: no errors.

- [ ] **Step 3: Commit**

```bash
git add resources/js/components/experience/SignageStrip.vue
git commit -m "$(cat <<'EOF'
Add SignageStrip component

A row of anchor chips that scroll-jump to in-page section IDs.
Sticky on desktop (>= 1040 px), horizontally scrollable on mobile,
with a discreet hover micro-tilt gated on prefers-reduced-motion.
Uses --sw-accent-violet (the wayfinding blue) for eyebrow and
hover background tint.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 6: Create `EditorialSpread.vue`

**Files:**

- Create: `resources/js/components/experience/EditorialSpread.vue`

- [ ] **Step 1: Write the component**

```vue
<script setup lang="ts">
defineProps<{
    id: string;
    eyebrow: string;
    title: string;
    summary: string;
    paragraphs: string[];
    pills?: string[];
    items?: string[];
    railLabel?: string;
    marginalia?: { author: string; quote: string };
}>();
</script>

<template>
    <article :id="id" class="editorial-spread">
        <header class="editorial-spread__head">
            <p class="type-eyebrow editorial-spread__eyebrow">
                {{ eyebrow }}
            </p>
            <h2 class="type-h1 editorial-spread__title">{{ title }}</h2>
            <p class="type-body-lg editorial-spread__summary">
                {{ summary }}
            </p>
        </header>
        <div class="editorial-spread__body">
            <div class="editorial-spread__prose">
                <p
                    v-for="paragraph in paragraphs"
                    :key="paragraph"
                    class="type-body editorial-spread__paragraph"
                >
                    {{ paragraph }}
                </p>
            </div>
            <aside class="editorial-spread__rail">
                <p
                    v-if="railLabel"
                    class="type-eyebrow editorial-spread__rail-label"
                >
                    {{ railLabel }}
                </p>
                <div v-if="pills?.length" class="editorial-spread__pills">
                    <span
                        v-for="pill in pills"
                        :key="pill"
                        class="editorial-spread__pill"
                    >
                        {{ pill }}
                    </span>
                </div>
                <ul v-if="items?.length" class="editorial-spread__items">
                    <li
                        v-for="item in items"
                        :key="item"
                        class="editorial-spread__item"
                    >
                        {{ item }}
                    </li>
                </ul>
                <figure v-if="marginalia" class="editorial-spread__marginalia">
                    <blockquote class="editorial-spread__quote">
                        {{ marginalia.quote }}
                    </blockquote>
                    <figcaption class="editorial-spread__author">
                        — {{ marginalia.author }}
                    </figcaption>
                </figure>
            </aside>
        </div>
    </article>
</template>

<style scoped>
.editorial-spread {
    display: grid;
    gap: var(--sw-space-sm);
    scroll-margin-top: calc(var(--sw-header-offset) + var(--sw-space-sm));
    min-width: 0;
}

.editorial-spread__head {
    display: grid;
    gap: var(--sw-space-3xs);
    max-width: 56rem;
}

.editorial-spread__eyebrow {
    color: var(--sw-accent-violet);
}

.editorial-spread__title {
    margin: 0;
    color: var(--sw-text-primary);
}

.editorial-spread__summary {
    margin: 0;
    color: var(--sw-text-secondary);
    text-wrap: pretty;
    max-width: 52rem;
}

.editorial-spread__body {
    display: grid;
    gap: var(--sw-space-md);
    min-width: 0;
}

@media (min-width: 920px) {
    .editorial-spread__body {
        grid-template-columns: minmax(0, 1.6fr) minmax(14rem, 1fr);
    }
}

.editorial-spread__prose {
    display: grid;
    gap: var(--sw-space-xs);
    min-width: 0;
}

.editorial-spread__paragraph {
    margin: 0;
    max-width: 62ch;
    color: var(--sw-text-secondary);
    text-wrap: pretty;
}

.editorial-spread__rail {
    display: grid;
    gap: var(--sw-space-sm);
    min-width: 0;
    padding: var(--sw-space-sm);
    border-left: 1px solid color-mix(in srgb, var(--sw-border) 70%, transparent);
}

@media (max-width: 919px) {
    .editorial-spread__rail {
        border-left: none;
        border-top: 1px solid
            color-mix(in srgb, var(--sw-border) 70%, transparent);
        padding: var(--sw-space-sm) 0 0;
    }
}

.editorial-spread__rail-label {
    margin: 0;
    color: color-mix(
        in srgb,
        var(--sw-text-secondary) 84%,
        var(--sw-text-primary)
    );
}

.editorial-spread__pills {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.editorial-spread__pill {
    font-family: var(--sw-font-body);
    font-size: 0.78rem;
    font-weight: 600;
    color: color-mix(
        in srgb,
        var(--sw-text-primary) 70%,
        var(--sw-text-secondary)
    );
}

.editorial-spread__items {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.editorial-spread__item {
    margin: 0;
    font-size: 0.92rem;
    line-height: 1.55;
    color: var(--sw-text-secondary);
}

.editorial-spread__marginalia {
    margin: 0;
    padding: var(--sw-space-xs) 0 0;
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 60%, transparent);
}

.editorial-spread__quote {
    margin: 0;
    font-family: var(--sw-font-display);
    font-style: italic;
    font-size: 1.05rem;
    line-height: 1.4;
    color: var(--sw-text-primary);
}

.editorial-spread__author {
    margin: var(--sw-space-3xs) 0 0;
    font-family: var(--sw-font-heading);
    font-size: 0.7rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--sw-text-muted);
}

@supports (animation-timeline: view()) {
    @media (prefers-reduced-motion: no-preference) {
        .editorial-spread__head {
            animation: editorial-spread-reveal linear both;
            animation-timeline: view();
            animation-range: entry 10% cover 30%;
        }
    }
}

@keyframes editorial-spread-reveal {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
```

- [ ] **Step 2: Type-check**

```bash
npm run types:check
```

Expected: no errors.

- [ ] **Step 3: Commit**

```bash
git add resources/js/components/experience/EditorialSpread.vue
git commit -m "$(cat <<'EOF'
Add EditorialSpread component

Two-column desktop layout (prose + side-rail with pills, items,
optional marginalia figure). Scroll-driven intertitle reveal via
animation-timeline: view(), gated on @supports and
prefers-reduced-motion. Mobile collapses to a single column with
the rail below the prose.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 7: Create `DataPlate.vue`

**Files:**

- Create: `resources/js/components/experience/DataPlate.vue`

- [ ] **Step 1: Write the component**

```vue
<script setup lang="ts">
defineProps<{
    trajectory: Array<{ title: string; summary: string }>;
    strengths: string[];
    focusAreas: Array<{ title: string; summary: string }>;
    trajectoryLabel: string;
    strengthsLabel: string;
    focusAreasLabel: string;
}>();
</script>

<template>
    <section class="data-plate">
        <div class="data-plate__column">
            <p class="type-eyebrow data-plate__label">
                {{ trajectoryLabel }}
            </p>
            <ul class="data-plate__list">
                <li
                    v-for="row in trajectory"
                    :key="row.title"
                    class="data-plate__item"
                >
                    <p class="type-h3 data-plate__item-title">
                        {{ row.title }}
                    </p>
                    <p class="type-body data-plate__item-summary">
                        {{ row.summary }}
                    </p>
                </li>
            </ul>
        </div>

        <div class="data-plate__column">
            <p class="type-eyebrow data-plate__label">
                {{ strengthsLabel }}
            </p>
            <ul class="data-plate__list data-plate__list--terse">
                <li
                    v-for="line in strengths"
                    :key="line"
                    class="data-plate__item-terse"
                >
                    {{ line }}
                </li>
            </ul>
        </div>

        <div class="data-plate__column">
            <p class="type-eyebrow data-plate__label">
                {{ focusAreasLabel }}
            </p>
            <ul class="data-plate__list">
                <li
                    v-for="row in focusAreas"
                    :key="row.title"
                    class="data-plate__item"
                >
                    <p class="type-h3 data-plate__item-title">
                        {{ row.title }}
                    </p>
                    <p class="type-body data-plate__item-summary">
                        {{ row.summary }}
                    </p>
                </li>
            </ul>
        </div>
    </section>
</template>

<style scoped>
.data-plate {
    display: grid;
    gap: var(--sw-space-md);
    padding: var(--sw-space-md);
    border: 1px solid color-mix(in srgb, var(--sw-border) 72%, transparent);
    border-radius: var(--sw-radius-lg);
    background: color-mix(in srgb, var(--sw-bg-surface) 92%, transparent);
    -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
    backdrop-filter: var(--sw-surface-backdrop-filter);
    min-width: 0;
}

@media (min-width: 920px) {
    .data-plate {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

.data-plate__column {
    display: grid;
    gap: var(--sw-space-xs);
    min-width: 0;
}

.data-plate__label {
    margin: 0;
    color: var(--sw-accent-violet);
}

.data-plate__list {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.data-plate__list--terse {
    gap: var(--sw-space-3xs);
}

.data-plate__item {
    display: grid;
    gap: 4px;
    padding-top: var(--sw-space-3xs);
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 56%, transparent);
}

.data-plate__item:first-child {
    border-top: 0;
    padding-top: 0;
}

.data-plate__item-title {
    margin: 0;
    color: var(--sw-text-primary);
}

.data-plate__item-summary {
    margin: 0;
    color: var(--sw-text-secondary);
    text-wrap: pretty;
}

.data-plate__item-terse {
    margin: 0;
    font-size: 0.96rem;
    line-height: 1.55;
    color: var(--sw-text-secondary);
}

.data-plate__item-terse::before {
    content: '— ';
    color: var(--sw-accent-dominant);
    margin-right: 0.3em;
}
</style>
```

- [ ] **Step 2: Type-check**

```bash
npm run types:check
```

Expected: no errors.

- [ ] **Step 3: Commit**

```bash
git add resources/js/components/experience/DataPlate.vue
git commit -m "$(cat <<'EOF'
Add DataPlate component

Three-column typographic plate consolidating trajectory,
strengths, and focus areas. Stacks on tablet and below, expands
to three equal columns at >= 920 px. Uses --sw-accent-violet for
eyebrow labels and --sw-accent-dominant as the bullet-mark color
on the strengths list.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 8: Refactor `Projects.vue` template

**Files:**

- Modify: `resources/js/pages/Projects.vue` — full rewrite of `<template>` and `<style scoped>`. Imports and `<script setup>` are largely preserved; the `copy` computed gets a few new keys; new `signageItems` and `allSpreads` computeds appear.

- [ ] **Step 1: Update the `<script setup>` block**

Replace the existing `<script setup>` with the version below. Compared to the previous version: imports the four new components, drops `Panel` and `LegendChip` and `PublicationWidget` (no longer used in the rewrite — keep `Panel` since the closing block still uses it), defines `signageItems` and `allSpreads`, extends `copy` with the new labels.

```ts
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import DataPlate from '@/components/experience/DataPlate.vue';
import EditorialSpread from '@/components/experience/EditorialSpread.vue';
import ManifestoOpener from '@/components/experience/ManifestoOpener.vue';
import SignageStrip from '@/components/experience/SignageStrip.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload, SiteProps } from '@/types';

type ExperienceSection = {
    title: string;
    eyebrow: string;
    summary: string;
    paragraphs: string[];
    detail_groups: Array<{
        title: string;
        items: string[];
        pills?: string[];
    }>;
    marginalia?: { author: string; quote: string };
};

const page = usePage<{ site: SiteProps }>();

const props = defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    thesis: string;
    positioning: string[];
    contexts: string[];
    professionalSections: ExperienceSection[];
    associativeSections: ExperienceSection[];
    sideProjectSections: ExperienceSection[];
    trajectory: Array<{ title: string; summary: string }>;
    strengths: string[];
    focusAreas: Array<{ title: string; summary: string }>;
    lookingFor: string;
    cvDownloads: Array<{ label: string; href: string }>;
}>();

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              overviewCta: 'Découvrir toutes les études de cas',
              contactCta: "Discuter d'un contexte proche",
              openerEyebrow: 'Comment je travaille',
              signageAriaLabel: 'Aller à un projet',
              spreadStackLabel: 'Stack',
              trajectoryLabel: 'Parcours',
              strengthsLabel: 'Forces',
              focusAreasLabel: 'Domaines',
              lookingForLabel: 'Ce que je recherche',
          }
        : {
              overviewCta: 'Browse all case studies',
              contactCta: 'Discuss a similar context',
              openerEyebrow: 'How I work',
              signageAriaLabel: 'Jump to a project',
              spreadStackLabel: 'Stack',
              trajectoryLabel: 'Trajectory',
              strengthsLabel: 'Strengths',
              focusAreasLabel: 'Focus areas',
              lookingForLabel: 'What I am looking for',
          },
);

function slugify(input: string): string {
    return input
        .toLowerCase()
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

type Spread = ExperienceSection & {
    id: string;
    pills: string[];
    items: string[];
};

function toSpread(section: ExperienceSection): Spread {
    const stackGroup = section.detail_groups.find(
        (group) => group.pills?.length,
    );
    const itemsGroup = section.detail_groups[0];
    return {
        ...section,
        id: slugify(section.title),
        pills: stackGroup?.pills ?? [],
        items: itemsGroup?.items ?? [],
    };
}

const allSpreads = computed<Spread[]>(() => [
    ...props.professionalSections.map(toSpread),
    ...props.associativeSections.map(toSpread),
    ...props.sideProjectSections.map(toSpread),
]);

const signageItems = computed(() =>
    allSpreads.value.map((spread) => ({
        id: spread.id,
        eyebrow: spread.eyebrow,
        label: spread.title,
    })),
);
</script>
```

- [ ] **Step 2: Replace the `<template>` block**

```vue
<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="experience-page">
            <ManifestoOpener
                :eyebrow="copy.openerEyebrow"
                :thesis="props.thesis"
                :summary="props.hero.summary"
            >
                <template #actions>
                    <Button href="/case-studies" variant="primary">
                        {{ copy.overviewCta }}
                    </Button>
                    <Button href="/contact" variant="ghost" arrow>
                        {{ copy.contactCta }}
                    </Button>
                </template>
            </ManifestoOpener>

            <SignageStrip
                v-if="signageItems.length"
                :items="signageItems"
                :aria-label="copy.signageAriaLabel"
            />

            <div class="experience-page__spreads">
                <EditorialSpread
                    v-for="spread in allSpreads"
                    :key="spread.id"
                    :id="spread.id"
                    :eyebrow="spread.eyebrow"
                    :title="spread.title"
                    :summary="spread.summary"
                    :paragraphs="spread.paragraphs"
                    :pills="spread.pills"
                    :items="spread.items"
                    :rail-label="copy.spreadStackLabel"
                    :marginalia="spread.marginalia"
                />
            </div>

            <DataPlate
                :trajectory="props.trajectory"
                :strengths="props.strengths"
                :focus-areas="props.focusAreas"
                :trajectory-label="copy.trajectoryLabel"
                :strengths-label="copy.strengthsLabel"
                :focus-areas-label="copy.focusAreasLabel"
            />

            <Panel class="experience-page__closer" tone="grid">
                <p class="type-eyebrow">{{ copy.lookingForLabel }}</p>
                <p class="type-body">{{ props.lookingFor }}</p>
                <div class="experience-page__closer-actions">
                    <Button href="/case-studies" variant="primary">
                        {{ copy.overviewCta }}
                    </Button>
                    <Button href="/contact" variant="ghost" arrow>
                        {{ copy.contactCta }}
                    </Button>
                </div>
            </Panel>
        </section>
    </SiteLayout>
</template>
```

- [ ] **Step 3: Replace the `<style scoped>` block**

```vue
<style scoped>
.experience-page {
    display: grid;
    gap: var(--sw-space-md);
    min-width: 0;
}

.experience-page__spreads {
    display: grid;
    gap: var(--sw-space-2xl);
    min-width: 0;
}

.experience-page__closer {
    display: grid;
    gap: var(--sw-space-sm);
    padding: clamp(24px, 3.2vw, 36px);
    margin-block: clamp(12px, 2vw, 20px);
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

.experience-page__closer-actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

@media (max-width: 640px) {
    .experience-page {
        gap: var(--sw-space-sm);
    }

    .experience-page__spreads {
        gap: var(--sw-space-xl);
    }
}
</style>
```

- [ ] **Step 4: Build**

```bash
npm run build
```

Expected: build succeeds. If a Vue type error surfaces (e.g., a prop key mismatch), fix it before continuing.

- [ ] **Step 5: Commit**

```bash
git add resources/js/pages/Projects.vue
git commit -m "$(cat <<'EOF'
Refactor experience page to the iceberg composition

Replaces the 3-Panel work-grid + linear story articles + four
publication widgets with five blocks: ManifestoOpener (one-line
thesis), SignageStrip (sticky chips that scroll-jump to spreads),
one EditorialSpread per professional / associative section, a
three-column DataPlate (trajectory + strengths + focus areas),
and a thin closing Panel with looking-for copy and two CTAs.

Frontmatter keys for the dropped surfaces (stack_groups,
career_snapshot, the publication widgets, side_project widgets)
remain in fr/en experience.md so i18n parity is preserved; they
are simply unused on the page.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 9: Phase 2 verification

This task does not produce a code change beyond a verification record.

**Files:**

- Create: `docs/superpowers/plans/2026-05-08-experience-page-rework-phase-2-results.md`

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

Expected: all tests pass, including the new `thesis` assertion.

- [ ] **Step 3: Subagent passes**

Dispatch in parallel:

- `i18n-parity-reviewer` on `resources/content/pages/` — expected: 7 slugs aligned, 0 drift.
- `design-conformance-reviewer` on:
    - `resources/js/components/experience/ManifestoOpener.vue`
    - `resources/js/components/experience/SignageStrip.vue`
    - `resources/js/components/experience/EditorialSpread.vue`
    - `resources/js/components/experience/DataPlate.vue`
    - `resources/js/pages/Projects.vue`

Expected: zero HIGH findings on the new files. Note any MEDIUM that warrants follow-up.

- [ ] **Step 4: Manual two-theme inspection**

Load `/fr/experience` and `/en/experience` in both `morning` and `sunset` themes. Verify:

- ManifestoOpener thesis renders large, balanced (text-wrap), with eyebrow above and 3-line summary below.
- SignageStrip displays four chips on desktop (sticky), scrollable on mobile. Click jumps to the matching spread.
- EditorialSpread displays prose left, side-rail right (>= 920 px). Pills, items, optional marginalia render. Intertitle reveals on scroll (Chrome / Edge / Safari 26).
- DataPlate displays three columns >= 920 px, stacks below.
- Closing Panel: looking-for line, two CTAs. Border tinted via `--sw-accent-violet` mix.

In sunset theme: hero gradient and signage chip eyebrow read in glowing variants. Reduced-motion (OS-level): all reveal animations collapse to instant.

- [ ] **Step 5: Lighthouse on `/fr/experience`**

```bash
npm run audit:lighthouse
npm run audit:lighthouse:mobile
```

Expected: Performance score equal to or better than the Phase 1 baseline.

- [ ] **Step 6: Write the verification record**

Create `docs/superpowers/plans/2026-05-08-experience-page-rework-phase-2-results.md` with this structure:

```markdown
# Experience Page Rework Phase 2 — Verification Results

**Completed:** YYYY-MM-DD
**Branch:** main
**Commits in scope:** <first sha> → <last sha>

## Tasks executed

| #   | Task                           | Status        |
| --- | ------------------------------ | ------------- |
| 1   | Read foundational files        | done          |
| 2   | Add thesis to FR + EN content  | done          |
| 3   | Backend pass-through and types | done          |
| 4   | ManifestoOpener.vue            | done          |
| 5   | SignageStrip.vue               | done          |
| 6   | EditorialSpread.vue            | done          |
| 7   | DataPlate.vue                  | done          |
| 8   | Projects.vue refactor          | done          |
| 9   | Verification + record          | this document |

## Lighthouse delta

| Metric      | After Phase 1 | After Phase 2 | Δ   |
| ----------- | ------------- | ------------- | --- |
| Performance | …             | …             | …   |
| FCP         | …             | …             | …   |
| LCP         | …             | …             | …   |
| CLS         | …             | …             | …   |

## Subagent passes

- i18n-parity-reviewer: <result>
- design-conformance-reviewer: <high>/<medium>/<low>

## Manual two-theme inspection

(checklist of pages and verdicts)

## Outstanding items / deferred

- Marginalia copy per professional/associative section. The component renders nothing when `marginalia` is absent; this Phase 2 ships without seeded marginalia. Owner adds when ready.
- `stack_groups` and `career_snapshot` frontmatter remain unused on the page. Re-evaluate in Phase 3 whether to surface them on `/colophon` or remove from frontmatter.

## Verdict

Phase 2 is <READY TO SHIP / NEEDS WORK>.
```

- [ ] **Step 7: Commit the verification record**

```bash
git add docs/superpowers/plans/2026-05-08-experience-page-rework-phase-2-results.md
git commit -m "$(cat <<'EOF'
Record Phase 2 experience-page rework verification results

Captures Lighthouse delta vs Phase 1 baseline, subagent passes,
manual two-theme inspection, and outstanding items deferred to
Phase 3.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

- [ ] **Step 8: Push to origin/main**

```bash
git push origin main
```

This triggers the Vercel auto-deploy. Phase 2 ships.
