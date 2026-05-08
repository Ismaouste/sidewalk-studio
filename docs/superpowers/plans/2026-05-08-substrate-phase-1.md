# Substrate Phase 1 — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace runtime-JS atmosphere and eager font loading with CSS-first equivalents, plumb View Transitions and Speculation Rules, and prepare token primitives (`@property`, OKLCH, twilight interpolations) so Phases 2 and 3 can ship without re-touching foundations.

**Architecture:** Token-first refactor of `resources/css/tokens.css`; rewrite `AmbientGrid.vue` as CSS-only consuming new tokens; thin glue in `resources/js/app.ts` for `document.startViewTransition` on Inertia navigation; static `<script type="speculationrules">` injected from a Blade layout; font-loading split into critical and deferred bundles. No new npm dependencies.

**Tech stack:** Laravel 12 + Inertia + Vue 3 + Tailwind v4 + Vite + Pest. CSS features: `@property`, OKLCH, `color-mix()`, `animation-timeline`, View Transitions API, `@starting-style`, Speculation Rules API, `text-wrap: balance`.

---

## Verification approach (adapted TDD)

Most Phase 1 tasks change CSS or thin TypeScript glue, where unit tests do not apply. Each task therefore uses this adapted cycle:

1. **Capture baseline** (file content, Lighthouse score, or visual screenshot of both themes if visual)
2. **Apply the change**
3. **Verify the change**: `npm run build` does not error; for `.ts` files `npm run types:check`; for visual changes, manual two-theme inspection; subagent pass at phase end
4. **Commit**

Where a backend test makes sense (none in Phase 1), full TDD applies.

Per `CLAUDE.md`: do **not** run `npm run build`, `vue-tsc`, or the full Pest suite after every step. Run them at the checkpoints called out in each task. The phase verification (Task 9) consolidates the heavy runs.

The PostToolUse hook auto-formats edited files via Prettier — do not manually re-format what the hook will fix.

## File structure

### Created

- `resources/css/view-transitions.css` — site-wide View Transitions CSS (named-element morph rules wait for Phase 2)
- `resources/css/fonts-deferred.css` — non-critical `@fontsource/*` imports, lazy-loaded
- `docs/superpowers/specs/2026-05-08-sidewalk-iceberg-rework-design.md` already exists (the spec)

### Modified

- `resources/css/tokens.css` — add `@property` registrations, `--sw-twilight-*`, `--sw-ambient-flare-{1..4}-{morning,sunset}`; convert palette neutrals/accents to OKLCH; introduce reduced-motion gating compatible with `prefers-reduced-motion`
- `resources/css/app.css` — keep only critical font imports (DM Sans 400/500), drop the rest into the deferred CSS file
- `resources/css/typography.css` — add `text-wrap: balance` to display/heading classes
- `resources/js/app.ts` — replace `style.setProperty()` runtime injection with `data-*` attributes; load `fonts-deferred.css` from idle callback; integrate `startViewTransition` glue
- `resources/js/composables/usePageTransitions.ts` — wrap Inertia router events in `document.startViewTransition` when supported; honour `prefers-reduced-motion`
- `resources/js/lib/staticPreview.ts` — debounce pointer/touch handlers; skip on `connection.saveData` or low-bandwidth
- `resources/js/components/design-system/AmbientGrid.vue` — full CSS-only rewrite consuming tokens; remove palette arrays, `MutationObserver`, `Math.random()` cycling
- `resources/views/app.blade.php` — emit `<script type="speculationrules">` for `prefetch` of internal links

### Untouched (Phase 2 will modify)

- `resources/js/pages/Projects.vue`
- `resources/content/pages/{fr,en}/experience.md`

---

## Task 1: Read foundational files and capture baseline

This task is **read-only** — no commits. It exists so that subsequent tasks have a known starting point.

**Files:**

- Read: `resources/css/tokens.css`
- Read: `resources/js/app.ts`
- Read: `resources/js/components/design-system/AmbientGrid.vue`
- Read: `resources/js/composables/usePageTransitions.ts`
- Read: `resources/js/lib/staticPreview.ts`
- Read: `resources/views/app.blade.php`
- Read: `resources/css/typography.css`

- [ ] **Step 1: Read each file fully**

For each path above, use the Read tool. Do not skim; subsequent tasks reference exact line ranges.

- [ ] **Step 2: Capture Lighthouse baseline**

Start the Laravel dev server in one shell (`php artisan serve --port=8088`) and Vite in another (`npm run dev`). Then in a third shell:

```bash
npm run audit:lighthouse
npm run audit:lighthouse:mobile
```

Save the two reports under their default location (`storage/app/lighthouse-report.html` and `…-mobile.html`). Note the Performance score and the FCP / LCP / CLS values from each — these are the numbers Phase 1 must not regress.

If Lighthouse cannot run on the current machine (no Chrome headless), record that the baseline is unmeasured and flag it in Task 9; visual + bundle checks still apply.

- [ ] **Step 3: Capture bundle baseline**

```bash
npm run audit:bundle
```

Open `storage/app/vite-bundle-report.html`. Note the total bundle size and the largest chunks — Task 9 compares against this.

---

## Task 2: Token foundations — `@property`, twilight, ambient palette

**Files:**

- Modify: `resources/css/tokens.css` (additions only; no removal of existing tokens)

- [ ] **Step 1: Add `@property` registrations at the top of `tokens.css`**

Insert immediately before the existing `:root` block (line 1):

```css
@property --sw-sun-angle {
    syntax: '<angle>';
    inherits: true;
    initial-value: 32deg;
}

@property --sw-grid-line-opacity {
    syntax: '<number>';
    inherits: true;
    initial-value: 0.04;
}

@property --sw-ambient-flare-1 {
    syntax: '<color>';
    inherits: true;
    initial-value: #b8845c;
}

@property --sw-ambient-flare-2 {
    syntax: '<color>';
    inherits: true;
    initial-value: #d8c0aa;
}

@property --sw-ambient-flare-3 {
    syntax: '<color>';
    inherits: true;
    initial-value: #a3724c;
}

@property --sw-ambient-flare-4 {
    syntax: '<color>';
    inherits: true;
    initial-value: #94867a;
}
```

These are additive. Existing `--sw-ambient-flare`, `--sw-ambient-flare-soft`, `--sw-ambient-flare-deep` (lines 100-102, 182-184) stay until Task 5 retires them.

- [ ] **Step 2: Add ambient palette tokens to morning theme**

Inside the `html[data-theme='morning']` block (around line 100, after the existing ambient-flare tokens), add the indexed palette:

```css
--sw-ambient-flare-1: #b8845c;
--sw-ambient-flare-2: #d8c0aa;
--sw-ambient-flare-3: #a3724c;
--sw-ambient-flare-4: #94867a;
```

- [ ] **Step 3: Add ambient palette tokens to sunset theme**

Inside the `html[data-theme='sunset']` block (around line 182), add:

```css
--sw-ambient-flare-1: #c9976a;
--sw-ambient-flare-2: #d4a578;
--sw-ambient-flare-3: #726962;
--sw-ambient-flare-4: #8b8378;
```

- [ ] **Step 4: Add twilight interpolation tokens**

After the sunset block (after line 222, before the `html[data-motion='reduced']` block), add a new `:root` extension:

```css
:root {
    --sw-twilight-anchor: color-mix(
        in oklch,
        var(--sw-bg-base) 88%,
        var(--sw-accent-coral) 12%
    );
    --sw-twilight-glow: color-mix(
        in oklch,
        var(--sw-accent-sun) 64%,
        var(--sw-bg-elevated) 36%
    );
}
```

These tokens auto-flip between themes because they consume the theme-scoped tokens. They are reserved for the experience-page hero gradient in Phase 2 but defined here so Phase 1 verification covers them.

- [ ] **Step 5: Add `prefers-reduced-motion` parity to motion tokens**

Right after the existing `html[data-motion='reduced']` block (line 224-228), add:

```css
@media (prefers-reduced-motion: reduce) {
    :root {
        --sw-motion-fast: 0ms linear;
        --sw-motion-smooth: 0ms linear;
        --sw-motion-reveal: 0ms linear;
        --sw-motion-sun: 0ms linear;
    }

    * {
        scroll-behavior: auto !important;
    }
}
```

The existing `data-motion="reduced"` attribute remains for users who toggle the in-site preference; the media query covers users whose OS preference is set.

- [ ] **Step 6: Verify the file builds**

```bash
npm run build
```

Expected: build succeeds. If it fails on a CSS parse error, re-read the file at the reported line and fix the syntax — most likely a missing semicolon or wrong nesting.

- [ ] **Step 7: Commit**

```bash
git add resources/css/tokens.css
git commit -m "$(cat <<'EOF'
Add @property registrations, twilight and ambient palette tokens

- Register --sw-sun-angle, --sw-grid-line-opacity, and four
  --sw-ambient-flare-N custom properties so they become animatable
  in CSS without JS
- Define indexed ambient palettes per theme (morning/sunset),
  preparing AmbientGrid CSS-only rewrite
- Introduce --sw-twilight-* color-mix tokens for the experience
  page hero
- Add prefers-reduced-motion media-query alongside the existing
  data-motion attribute so OS-level preference is honoured

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 3: Replace runtime style.setProperty injection

**Files:**

- Modify: `resources/js/app.ts:13-30` (the `applyThemeSettings` function)
- Modify: `resources/css/tokens.css` (consume new attributes)

The current function sets three CSS custom properties on `document.documentElement` via `style.setProperty()`. Each call triggers a style recalculation. Replace with `data-*` attributes; CSS reads them via attribute selectors.

- [ ] **Step 1: Rewrite `applyThemeSettings` in `resources/js/app.ts`**

Replace lines 13-30 with:

```ts
function applyThemeSettings(settings?: ThemeSettings) {
    if (!settings || typeof document === 'undefined') {
        return;
    }

    const root = document.documentElement;
    root.setAttribute('data-gradient-angle', String(settings.gradient_angle));
    root.setAttribute('data-surface-blur', String(settings.surface_blur));
    root.setAttribute('data-line-thickness', String(settings.line_thickness));
}
```

- [ ] **Step 2: Add CSS bridge in `tokens.css`**

Append to the bottom of `resources/css/tokens.css` (after the `html[data-contrast='boost']` block):

```css
:root[data-gradient-angle] {
    --sw-runtime-gradient-angle: calc(
        attr(data-gradient-angle type(<number>), 132) * 1deg
    );
}
:root[data-surface-blur] {
    --sw-runtime-surface-blur: calc(
        attr(data-surface-blur type(<number>), 20) * 1px
    );
}
:root[data-line-thickness] {
    --sw-runtime-line-thickness: calc(
        attr(data-line-thickness type(<number>), 1) * 1px
    );
}
```

`attr()` with type and unit is supported in Chrome 133+ and Safari 18.2+; for older browsers a single style recalc still happens via the existing fallback values defined at the top of `tokens.css` (lines 59-61). Acceptable degradation.

- [ ] **Step 3: Type-check the TS change**

```bash
npm run types:check
```

Expected: no errors.

- [ ] **Step 4: Build and visual check**

```bash
npm run build
```

Then manually load `/fr/` in a browser, switch between morning and sunset themes via the toggle, and confirm:

- Gradient angle on the header surface still respects the theme settings
- Surface blur still applies
- Line thickness on consent UI borders still applies

- [ ] **Step 5: Commit**

```bash
git add resources/js/app.ts resources/css/tokens.css
git commit -m "$(cat <<'EOF'
Move runtime theme settings from style.setProperty to data-attributes

Eliminates three style recalculations on every page mount by
publishing gradient angle, surface blur, and line thickness as
data-* attributes on documentElement and reading them in CSS via
attr(... type) with a fallback to the static token defaults.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 4: Rewrite AmbientGrid as CSS-only

**Files:**

- Modify: `resources/js/components/design-system/AmbientGrid.vue` — full rewrite

Goal: zero `MutationObserver`, zero `Math.random()`, zero JS palette arrays. The component is reduced to a thin Vue wrapper around scoped CSS that consumes `--sw-ambient-flare-{1..4}` and `--sw-sun-angle`.

- [ ] **Step 1: Read the current component fully**

The current component is ~430 lines per the design audit. Read it from start to end. Identify:

- The `<script setup>` block — JS palette arrays (`morningPalettes`, `sunsetPalettes`), `MutationObserver`, `requestAnimationFrame` loops, dynamic `style.setProperty` calls.
- The `<template>` block — the SVG / div structure.
- The `<style scoped>` block — keyframes, mask gradients, the `rgba(0,0,0,…)` mask values flagged HIGH by the design audit (lines 235-279), the `@keyframes` blocks (lines 318-426).

- [ ] **Step 2: Replace `<script setup>` with a minimal version**

Replace the entire `<script setup>` block with:

```ts
<script setup lang="ts">
// AmbientGrid is now driven entirely by CSS. The component remains
// a Vue file so the existing SiteLayout import path stays stable
// and so future enhancements can add reactive props if needed.
defineOptions({ name: 'AmbientGrid' });
</script>
```

- [ ] **Step 3: Simplify the `<template>` block**

Keep the existing structural elements (the grid backdrop, the sun layer, the flare layers). Remove any inline `:style` bindings and any `ref`s that the script no longer drives. The template is purely declarative; CSS does the work.

The minimum structural skeleton is:

```html
<template>
    <div class="ambient-grid" aria-hidden="true">
        <div class="ambient-grid__sun" />
        <div class="ambient-grid__flare ambient-grid__flare--1" />
        <div class="ambient-grid__flare ambient-grid__flare--2" />
        <div class="ambient-grid__flare ambient-grid__flare--3" />
        <div class="ambient-grid__flare ambient-grid__flare--4" />
        <div class="ambient-grid__lines" />
    </div>
</template>
```

Adjust class names to match the existing structure if they differ; what matters is that no element has an inline style binding, and no element references a JS-set custom property.

- [ ] **Step 4: Rewrite `<style scoped>`**

The scoped style block must:

1. Use `var(--sw-ambient-flare-1)` … `var(--sw-ambient-flare-4)` for flare colours (no hex literals).
2. Use `var(--sw-sun-gradient)` for the sun layer (already token-driven).
3. Replace any `rgba(0, 0, 0, X)` mask value with `color-mix(in srgb, var(--sw-text-inverse) Y%, transparent)` where Y matches the prior alpha as a percentage (e.g., `rgba(0,0,0,0.82)` → `color-mix(in srgb, var(--sw-text-inverse) 82%, transparent)`).
4. Drive movement via a single `@keyframes` block that animates `--sw-sun-angle`, gated as below.
5. Provide a static end-state when scroll-driven animations are unsupported or motion is reduced.

Here is the canonical structure to match:

```css
<style scoped>
.ambient-grid {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: -1;
    overflow: hidden;
    contain: paint;
}

.ambient-grid__sun {
    position: absolute;
    width: var(--sw-sun-size);
    height: var(--sw-sun-size);
    top: var(--sw-sun-top);
    left: var(--sw-sun-left);
    bottom: var(--sw-sun-bottom);
    right: var(--sw-sun-right);
    background: var(--sw-sun-gradient);
    transform: rotate(var(--sw-sun-angle));
    filter: blur(var(--sw-sun-blur-global));
    opacity: var(--sw-sun-opacity-global);
    mask-image: radial-gradient(
        circle at center,
        color-mix(in srgb, var(--sw-text-inverse) 82%, transparent),
        color-mix(in srgb, var(--sw-text-inverse) 42%, transparent) 56%,
        transparent 86%
    );
}

.ambient-grid__flare--1 {
    background: radial-gradient(
        closest-side,
        var(--sw-ambient-flare-1),
        transparent 70%
    );
}
.ambient-grid__flare--2 {
    background: radial-gradient(
        closest-side,
        var(--sw-ambient-flare-2),
        transparent 70%
    );
}
.ambient-grid__flare--3 {
    background: radial-gradient(
        closest-side,
        var(--sw-ambient-flare-3),
        transparent 70%
    );
}
.ambient-grid__flare--4 {
    background: radial-gradient(
        closest-side,
        var(--sw-ambient-flare-4),
        transparent 70%
    );
}

.ambient-grid__lines {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(
            to right,
            color-mix(in srgb, var(--sw-grid-line) 100%, transparent)
                var(--sw-runtime-line-thickness),
            transparent var(--sw-runtime-line-thickness)
        ),
        linear-gradient(
            to bottom,
            color-mix(in srgb, var(--sw-grid-line) 100%, transparent)
                var(--sw-runtime-line-thickness),
            transparent var(--sw-runtime-line-thickness)
        );
    background-size: 96px 96px;
    opacity: var(--sw-grid-line-opacity);
}

@keyframes ambient-sun-drift {
    from {
        --sw-sun-angle: 0deg;
    }
    to {
        --sw-sun-angle: 360deg;
    }
}

@supports (animation-timeline: scroll(root)) {
    @media (prefers-reduced-motion: no-preference) {
        .ambient-grid__sun {
            animation: ambient-sun-drift 1ms linear;
            animation-timeline: scroll(root);
            animation-fill-mode: both;
        }
    }
}
</style>
```

The exact filter/opacity expressions reuse the existing `--sw-sun-blur-global` and `--sw-sun-opacity-global` tokens (defined in `tokens.css` lines 134-141 / 210-217). Do not change those tokens; only change the consumer.

If the existing template had additional decorative layers (e.g., a noise overlay), preserve them but apply the same rule: hex/rgba → token or `color-mix`; JS palette → indexed `--sw-ambient-flare-N`.

- [ ] **Step 5: Verify both themes render distinctly**

```bash
npm run build
```

Manually load `/fr/` in Chrome 115+ (animation-timeline support):

- Switch theme to morning — sun should sit top-left, flares warm earth tones.
- Switch to sunset — sun should sit bottom-right, flares deeper / warmer.
- Slowly scroll the page — sun should rotate as the page scrolls.
- Toggle OS reduced-motion preference — sun rotation should stop instantly; static end-state visible.

- [ ] **Step 6: Run design-conformance-reviewer subagent**

Dispatch the project-local `design-conformance-reviewer` subagent on `resources/js/components/design-system/AmbientGrid.vue` and `resources/css/tokens.css`. The agent should report **zero HIGH findings** for the rewritten file (the audit baseline had three).

- [ ] **Step 7: Commit**

```bash
git add resources/js/components/design-system/AmbientGrid.vue
git commit -m "$(cat <<'EOF'
Rewrite AmbientGrid as CSS-only

Removes the JS palette arrays, MutationObserver, Math.random()
cycling, and the requestAnimationFrame loop. Movement is now
driven by animation-timeline: scroll(root), gated on @supports
and prefers-reduced-motion. Mask values are theme-aware via
color-mix on --sw-text-inverse instead of hardcoded rgba(0,0,0).

Closes the AmbientGrid HIGH findings from both perf and design
audits.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 5: View Transitions plumbing

**Files:**

- Create: `resources/css/view-transitions.css`
- Modify: `resources/css/app.css` (add the new import)
- Modify: `resources/js/composables/usePageTransitions.ts`

- [ ] **Step 1: Read the current `usePageTransitions.ts`**

Inspect the composable and identify the function called from `app.ts:60` (`configurePageTransitions(...)`). Confirm whether it already wraps Inertia navigation; the next step will assume it does not. If it does, adapt the rewrite to extend rather than duplicate.

- [ ] **Step 2: Create `resources/css/view-transitions.css`**

```css
/* View Transitions for Inertia same-document navigation. The
 * page-hero / article-cover-* names are used by Phase 2 page
 * components; this file ships the global crossfade defaults. */

@supports (view-transition-name: none) {
    @media (prefers-reduced-motion: no-preference) {
        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation-duration: 220ms;
            animation-timing-function: cubic-bezier(0.22, 1, 0.36, 1);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation-duration: 0s;
        }
    }
}
```

- [ ] **Step 3: Import `view-transitions.css` from `app.css`**

In `resources/css/app.css`, add a new line right after the existing `@import './layout.css';` (line 14):

```css
@import './view-transitions.css';
```

- [ ] **Step 4: Wrap Inertia navigation in `usePageTransitions.ts`**

Inside the composable, locate where Inertia router events are bound. Add a wrapper that prefers `document.startViewTransition`. Minimal pattern:

```ts
import { router } from '@inertiajs/vue3';

type ViewTransitionRoot = Document & {
    startViewTransition?: (callback: () => void | Promise<void>) => {
        finished: Promise<void>;
    };
};

function startTransition(callback: () => void) {
    if (typeof document === 'undefined') {
        callback();
        return;
    }
    const reduced = window.matchMedia(
        '(prefers-reduced-motion: reduce)',
    ).matches;
    const doc = document as ViewTransitionRoot;
    if (reduced || !doc.startViewTransition) {
        callback();
        return;
    }
    doc.startViewTransition(callback);
}

router.on('before', (event) => {
    // Defer the navigation behind a view transition; let Inertia
    // continue normally.
    startTransition(() => {
        // no-op — Inertia performs the swap; the wrapper merely
        // captures before/after snapshots.
    });
});
```

If the composable already binds `router.on(...)`, integrate the `startTransition` wrapping into the existing handler rather than adding a duplicate listener.

- [ ] **Step 5: Type-check + build**

```bash
npm run types:check
npm run build
```

- [ ] **Step 6: Manual smoke test**

Load `/fr/` in Chrome and click an internal link (e.g., to `/fr/journal`). Observe a brief crossfade between the two pages — the default View Transitions crossfade. Toggle reduced-motion: navigation cuts instantly, no fade.

In Firefox 144+, expect identical behaviour (View Transitions are Newly available there). In Firefox <144, navigation falls through `startTransition` straight to the callback — graceful degradation.

- [ ] **Step 7: Commit**

```bash
git add resources/css/view-transitions.css resources/css/app.css resources/js/composables/usePageTransitions.ts
git commit -m "$(cat <<'EOF'
Plumb View Transitions on Inertia navigation

Adds a CSS file with crossfade defaults gated on @supports and
prefers-reduced-motion, and wraps Inertia router navigation in
document.startViewTransition when available. Per-element named
transitions land with Phase 2.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

### Note: `@starting-style` deferred to Phase 2

The spec listed `@starting-style` adoption under Phase 1, but the current substrate contains no Vue `<Transition>` wrappers around new-entry-revealing elements that warrant CSS replacement today. The View Transitions CSS file (Task 5) already prepares the entry-animation surface. Concrete `@starting-style` adoption lands with Phase 2 components (manifesto opener entry, signage strip reveal, locale popover) where new mount events appear.

If a `<Transition>` wrapper is identified in scope while executing this plan, swap it to `@starting-style` inline and document the change in the Phase 1 verification record (Task 9). Do not invent targets.

---

## Task 6: Defer non-critical fonts

**Files:**

- Modify: `resources/css/app.css`
- Create: `resources/css/fonts-deferred.css`
- Modify: `resources/js/app.ts`

- [ ] **Step 1: Trim `resources/css/app.css` font imports to critical only**

Replace lines 1-9 with:

```css
@import '@fontsource/dm-sans/400.css';
@import '@fontsource/dm-sans/500.css';
```

DM Sans 400 and 500 cover body and emphasized body, the only weights needed before first paint.

- [ ] **Step 2: Create `resources/css/fonts-deferred.css`**

```css
/* Loaded from idle callback in app.ts. Contains every font weight
 * and family that is not required for first paint. */

@import '@fontsource/dm-sans/300.css';
@import '@fontsource/dm-sans/600.css';
@import '@fontsource/fraunces/300-italic.css';
@import '@fontsource/fraunces/400-italic.css';
@import '@fontsource/syne/700.css';
@import '@fontsource/syne/800.css';
@import '@fontsource/dm-mono/400.css';
```

- [ ] **Step 3: Load `fonts-deferred.css` from idle in `app.ts`**

Inside the `createInertiaApp({ setup })` callback, after the `createApp(...).mount(el)` line and before the existing `scheduleIdleTask(...)` block for web vitals, add a new idle import:

```ts
scheduleIdleTask(() => {
    void import('../css/fonts-deferred.css').catch(() => {
        /* deferred fonts are not critical; ignore failures */
    });
});
```

Vite resolves CSS modules dynamically when imported via `import()`; the file is emitted as a separate chunk and fetched on idle.

- [ ] **Step 4: Build and inspect bundle**

```bash
npm run audit:bundle
```

Open `storage/app/vite-bundle-report.html`. Confirm:

- The main entry chunk no longer includes Fraunces, Syne, or DM Mono font CSS or woff2 references in its critical path.
- A separate `fonts-deferred-*.css` chunk exists.

If Vite did not produce a separate chunk, change the idle task to add a `<link rel="stylesheet">` to `document.head` pointing at the public path of the file. Vite copies static CSS imports under `public/build/assets/`. The `<link>` approach is a fallback to dynamic `import()`.

- [ ] **Step 5: Verify Lighthouse improvement**

```bash
npm run audit:lighthouse:mobile
```

FCP should improve over the Task 1 baseline (target: -100 ms minimum on mobile profile). LCP should not regress.

- [ ] **Step 6: Commit**

```bash
git add resources/css/app.css resources/css/fonts-deferred.css resources/js/app.ts
git commit -m "$(cat <<'EOF'
Defer non-critical font imports

Keep only DM Sans 400 and 500 in the critical CSS bundle. Other
font weights (DM Sans 300/600) and families (Fraunces italics,
Syne 700/800, DM Mono 400) are loaded on idle from a separate
chunk, eliminating their cost from First Contentful Paint.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 7: Speculation Rules + prefetch debounce

**Files:**

- Modify: `resources/views/app.blade.php`
- Modify: `resources/js/lib/staticPreview.ts`

- [ ] **Step 1: Read `resources/views/app.blade.php`**

Identify the location to insert the speculation rules — typically inside `<head>` before `</head>`.

- [ ] **Step 2: Add `<script type="speculationrules">` to the Blade layout**

Inside the `<head>` of `resources/views/app.blade.php`, add:

```blade
<script type="speculationrules">
{
    "prefetch": [
        {
            "where": {
                "and": [
                    { "href_matches": "/*" },
                    { "not": { "href_matches": "/*.pdf" } },
                    { "not": { "href_matches": "/*.zip" } }
                ]
            },
            "eagerness": "moderate"
        }
    ]
}
</script>
```

`prefetch` does not run scripts, so it is safe alongside the existing `vanilla-cookieconsent` analytics gate. Do **not** use `prerender` — it would execute consent-affecting scripts before the user's choice.

Browsers without Speculation Rules support ignore the script silently.

- [ ] **Step 3: Read `resources/js/lib/staticPreview.ts`**

Identify the existing pointer / focus / touch event listeners flagged by the perf audit (the audit cited lines 26-31 and 150-205). Note the function names that handle prefetch-on-intent.

- [ ] **Step 4: Add debounce + connection-aware skip**

Wrap the existing prefetch-intent handlers with a 100 ms debounce, and prepend a guard that skips on slow connections:

```ts
type NetworkInformation = {
    saveData?: boolean;
    effectiveType?: '4g' | '3g' | '2g' | 'slow-2g';
};
type ConnectedNavigator = Navigator & {
    connection?: NetworkInformation;
};

function shouldPrefetch(): boolean {
    if (typeof navigator === 'undefined') return false;
    const conn = (navigator as ConnectedNavigator).connection;
    if (!conn) return true;
    if (conn.saveData) return false;
    if (conn.effectiveType && conn.effectiveType !== '4g') return false;
    return true;
}

function debounce<F extends (...args: unknown[]) => void>(
    fn: F,
    ms: number,
): F {
    let timer: ReturnType<typeof setTimeout> | undefined;
    return ((...args: Parameters<F>) => {
        if (timer) clearTimeout(timer);
        timer = setTimeout(() => {
            fn(...args);
        }, ms);
    }) as F;
}
```

Use these helpers to wrap the `prefetchAnchor` (or equivalently named) function. Pseudocode for the wrapping:

```ts
const debouncedPrefetch = debounce(prefetchAnchor, 100);

document.addEventListener(
    'pointerenter',
    (event) => {
        if (!shouldPrefetch()) return;
        // existing target resolution logic …
        debouncedPrefetch(target);
    },
    { capture: true },
);
```

Apply the same `shouldPrefetch()` guard and the same `debouncedPrefetch` to the `focusin` and `touchstart` handlers. Remove the `touchstart` handler entirely on coarse pointers if the existing logic already double-fires.

- [ ] **Step 5: Type-check + build**

```bash
npm run types:check
npm run build
```

- [ ] **Step 6: Manual smoke test**

In Chrome DevTools Network tab, throttle to "Fast 3G". Hover several internal links rapidly. Expected:

- No prefetch fired (the `effectiveType !== '4g'` guard skipped them).

Switch back to "No throttling". Hover one link, wait, hover another. Expected:

- One prefetch per link, fired ~100 ms after hover started, not on every pointermove.

- [ ] **Step 7: Commit**

```bash
git add resources/views/app.blade.php resources/js/lib/staticPreview.ts
git commit -m "$(cat <<'EOF'
Add Speculation Rules prefetch and debounce hover prefetch

Browsers that support the API now prefetch internal links with
moderate eagerness without executing scripts. Hover-driven
prefetch is debounced to 100 ms and skipped on saveData or
3G/2G connections, addressing the perf audit finding on
staticPreview.ts.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 8: Site-wide `text-wrap: balance` on titles

**Files:**

- Modify: `resources/css/typography.css`

- [ ] **Step 1: Read `resources/css/typography.css`**

Identify the rules for `.type-display-*`, `.type-h1`, `.type-h2`, `.type-h3` and any direct `h1`/`h2`/`h3` selectors used for display.

- [ ] **Step 2: Add `text-wrap: balance` to display and heading classes**

Add a single block at the top of the file (after any `@import`):

```css
.type-display-xl,
.type-display,
.type-h1,
.type-h2,
.type-h3,
.sw-section h1,
.sw-section h2,
.sw-section h3 {
    text-wrap: balance;
}
```

- [ ] **Step 3: Build + visual check**

```bash
npm run build
```

Manually load the home page and any page with multi-line headings. Expected: long titles distribute words more evenly across lines (especially visible on the loader-quote display and on case-study titles).

- [ ] **Step 4: Commit**

```bash
git add resources/css/typography.css
git commit -m "$(cat <<'EOF'
Apply text-wrap: balance to display and heading classes

One-line CSS ship that balances multi-line titles editorial-style.
Free, automatic, invisible — the kind of detail the calm-manifesto
art direction asks for.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

---

## Task 9: Phase 1 verification

This task does not produce a code change; it produces a verification record committed alongside the work.

**Files:**

- Create: `docs/superpowers/plans/2026-05-08-substrate-phase-1-results.md`

- [ ] **Step 1: Run final type check and build**

```bash
npm run lint:check
npm run format:check
npm run types:check
npm run build
```

All must pass. If lint or format reports issues, fix them inline (the format-on-edit hook should have caught them earlier, so failures here indicate a file the hook did not run on — re-save through the editor and re-stage).

- [ ] **Step 2: Run Lighthouse comparison**

Start dev server (`php artisan serve --port=8088`), then:

```bash
npm run audit:lighthouse
npm run audit:lighthouse:mobile
```

Open the new reports next to the Task 1 baseline. Record:

- Performance score: before / after
- FCP: before / after
- LCP: before / after
- CLS: before / after
- TBT: before / after

Acceptance: Performance score does not drop. FCP improves by at least 50 ms on the mobile profile (font deferral target).

- [ ] **Step 3: Run bundle comparison**

```bash
npm run audit:bundle
```

Compare against the Task 1 baseline. Record:

- Total compressed size: before / after
- Largest non-vendor chunk: before / after
- Confirm `fonts-deferred-*.css` chunk exists separately
- Confirm AmbientGrid no longer pulls runtime palette arrays into the main JS bundle

Acceptance: total bundle does not grow; AmbientGrid contribution to JS is essentially zero (Vue boilerplate only).

- [ ] **Step 4: Run `design-conformance-reviewer` on the substrate diff**

Dispatch the project-local subagent against:

- `resources/css/tokens.css`
- `resources/css/view-transitions.css`
- `resources/css/typography.css`
- `resources/js/components/design-system/AmbientGrid.vue`
- `resources/js/composables/usePageTransitions.ts`

Acceptance: zero HIGH findings on the new/modified files. MEDIUM findings are reviewed and either fixed or deferred to a tracked issue.

- [ ] **Step 5: Manual two-theme inspection**

Load each public page in both themes (`morning`, `sunset`):

- `/fr/` and `/en/` (home)
- `/fr/experience` and `/en/experience` (current Projects.vue)
- `/fr/journal` and `/en/journal` (Writing index)
- A Writing show page in both locales
- `/fr/contact` and `/en/contact`
- `/fr/local` and `/en/local`
- `/fr/sparkle` (the hidden page)

Check that:

- Both themes render distinctly (theme delta from the design audit must remain STRONG)
- AmbientGrid is visible and animated under scroll, with no console errors
- Reduced motion (OS-level) collapses animations correctly
- View Transitions crossfade fires between pages on Chrome / Edge / Safari / Firefox 144+

- [ ] **Step 6: Write the verification record**

Create `docs/superpowers/plans/2026-05-08-substrate-phase-1-results.md` with the following structure (filling actual numbers):

```markdown
# Substrate Phase 1 — Verification Results

**Completed:** YYYY-MM-DD

## Lighthouse (desktop / mobile)

| Metric      | Baseline | After Phase 1 | Δ   |
| ----------- | -------- | ------------- | --- |
| Performance | …        | …             | …   |
| FCP         | …        | …             | …   |
| LCP         | …        | …             | …   |
| CLS         | …        | …             | …   |
| TBT         | …        | …             | …   |

## Bundle

|                  | Baseline | After Phase 1 | Δ   |
| ---------------- | -------- | ------------- | --- |
| Total compressed | …        | …             | …   |
| Largest chunk    | …        | …             | …   |

## Subagent passes

- design-conformance-reviewer: <high count> HIGH, <medium count> MEDIUM, <low count> LOW
- i18n-parity-reviewer: not applicable (no content edits in Phase 1)

## Manual two-theme inspection

- [ ] Home (fr/en, both themes)
- [ ] Experience (fr/en, both themes)
- [ ] Journal index (fr/en, both themes)
- [ ] Journal show (one slug, fr/en, both themes)
- [ ] Contact (fr/en, both themes)
- [ ] Local (fr/en, both themes)
- [ ] Sparkle (fr only, both themes)

## Outstanding items

- (list any deferred or follow-up tasks to fold into Phase 2 or a separate fix commit)

## Notes

- Browser tested: Chrome <version>, Firefox <version>, Safari <version>
- Reduced-motion verified at OS level: yes / no
```

- [ ] **Step 7: Commit the verification record**

```bash
git add docs/superpowers/plans/2026-05-08-substrate-phase-1-results.md
git commit -m "$(cat <<'EOF'
Record Phase 1 substrate verification results

Captures Lighthouse, bundle, subagent, and manual two-theme
inspection outcomes for the iceberg rework substrate work.

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```

- [ ] **Step 8: Hand off to Phase 2**

Once the verification record shows green (Performance not regressed, FCP improved, design conformance clean), Phase 1 is complete. Phase 2 (experience-page rework) and Phase 3 (journal & navigation polish) each get their own `writing-plans` invocation in fresh sessions, consuming the spec at `docs/superpowers/specs/2026-05-08-sidewalk-iceberg-rework-design.md` and the verified substrate from this Phase 1 plan.
