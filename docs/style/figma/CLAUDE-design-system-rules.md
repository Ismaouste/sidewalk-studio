# CLAUDE.md — Sidewalk Studio
## Design System Rules for Figma MCP Integration

> Generated via `Figma:create_design_system_rules` — Vue · TypeScript · PHP · CSS
> Stack: Laravel 13 · Inertia.js 3 · Vue 3 · TypeScript · Vite 8 (Rolldown) — no CSS framework
> Theme: **Morning Grid** (light) · **Sunset Signal** (dark) — one system, two atmospheric states
> Author: Ismael Rodmacq · Nancy, Grand Est · March 2026

---

## 1. Token Definitions

### Where tokens are defined

```
resources/css/tokens.css   ← SINGLE SOURCE OF TRUTH for all --sw-* tokens
```

No JSON tokens. No Style Dictionary pipeline. No separate theme files.
Tokens go: `tokens.css` → scoped Vue `<style>` blocks. Base element
normalisation lives in `base.css` under `@layer reset`.

### Token format

CSS custom properties, prefix `--sw-`, three declaration blocks:

```css
/* 1. SHARED — constant across both themes */
:root {
  --sw-font-display: 'Fraunces', serif;
  --sw-radius-md: 8px;
  --sw-space-sm: 24px;
  /* … */
}

/* 2. MORNING GRID (default / light) */
:root,
[data-theme="morning"] {
  --sw-bg-base: #F4F1EA;
  --sw-accent-sun: #C97D0A;
  /* … */
}

/* 3. SUNSET SIGNAL (dark) */
[data-theme="sunset"] {
  --sw-bg-base: #0D0F1C;
  --sw-accent-sun: #F0700A;
  /* … */
}
```

Theme switch = `data-theme` attribute on `<html>`. No class toggling. No JS-injected inline colors.

### Shared tokens (constant, no mode)

| Token | Value | Role |
|---|---|---|
| `--sw-font-display` | `'Fraunces', serif` | Display/hero only |
| `--sw-font-heading` | `'Syne', sans-serif` | Labels, nav, eyebrow only |
| `--sw-font-body` | `'DM Sans', sans-serif` | All body text and UI |
| `--sw-font-code` | `'DM Mono', monospace` | Code only |
| `--sw-radius-none` | `0px` | Tables, dividers |
| `--sw-radius-sm` | `4px` | Chip, badge, inline code |
| `--sw-radius-md` | `8px` | Card, input, button |
| `--sw-radius-lg` | `16px` | Modal, large panel |
| `--sw-radius-xl` | `9999px` | SunAnchor **only** |
| `--sw-radius-full` | `9999px` | Avatar, dot indicator |
| `--sw-space-4xs` | `4px` | Micro gap |
| `--sw-space-3xs` | `8px` | Icon + label pair |
| `--sw-space-2xs` | `12px` | Chip internal padding |
| `--sw-space-xs` | `16px` | Tight gap |
| `--sw-space-sm` | `24px` | Card padding, component gap |
| `--sw-space-md` | `40px` | Column gap |
| `--sw-space-lg` | `64px` | Section padding-y |
| `--sw-space-xl` | `96px` | Major section padding-y |
| `--sw-space-2xl` | `128px` | Hero padding-y |
| `--sw-space-3xl` | `192px` | Atmospheric section |
| `--sw-motion-fast` | `120ms ease` | Hover, focus ring |
| `--sw-motion-smooth` | `280ms cubic-bezier(0.4,0,0.2,1)` | UI transitions |
| `--sw-motion-reveal` | `600ms cubic-bezier(0.22,1,0.36,1)` | Section entrance |
| `--sw-motion-sun` | `900ms cubic-bezier(0.4,0,0.2,1)` | SunAnchor position shift |

### Theme tokens — Morning Grid vs Sunset Signal

| Token | Morning Grid | Sunset Signal |
|---|---|---|
| `--sw-bg-base` | `#F4F1EA` | `#0D0F1C` |
| `--sw-bg-surface` | `#FAFAF6` | `#141728` |
| `--sw-bg-grid` | `#EAE6DC` | `#1A1D32` |
| `--sw-bg-elevated` | `#FFFFFF` | `#20243C` |
| `--sw-text-primary` | `#141210` | `#E8E2F2` |
| `--sw-text-secondary` | `#4A4138` | `#867EA0` |
| `--sw-text-muted` | `#8A8070` | `#443E5A` |
| `--sw-text-inverse` | `#F4F1EA` | `#0D0F1C` |
| `--sw-accent-dominant` | `#1E4B8F` | `#7C5CE8` |
| `--sw-accent-green` | `#1A6B4A` | `#2DD4A0` |
| `--sw-accent-sun` | `#C97D0A` | `#F0700A` |
| `--sw-accent-coral` | `#B83528` | `#D95880` |
| `--sw-accent-violet` | `#5B21B6` | `#5290E8` |
| `--sw-border` | `#D8D0C2` | `#252840` |
| `--sw-border-focus` | `#C97D0A` | `#F0700A` |
| `--sw-grid-line` | `rgba(30,75,143,.07)` | `rgba(124,92,232,.08)` |
| `--sw-shadow-sm` | `0 1px 4px rgba(20,18,16,.06)` | `0 1px 4px rgba(0,0,0,.25)` |
| `--sw-shadow-md` | `0 4px 16px rgba(20,18,16,.09)` | `0 4px 20px rgba(0,0,0,.35)` |
| `--sw-header-bg` | `linear-gradient(160deg,#B4CBE4,#D0E4F2,#EBF3FB)` | `linear-gradient(160deg,#1A0F3C,#1E1640,#0C0E1A)` |
| `--sw-sun-gradient` | `radial-gradient(circle at 38% 38%,#FBBF24EE,#C97D0A88 45%,transparent 85%)` | `radial-gradient(circle at 62% 62%,#F0700AEE,#7C5CE888 45%,transparent 85%)` |
| `--sw-sun-glow` | `rgba(201,125,10,.28)` | `rgba(240,112,10,.25)` |
| `--sw-sun-size` | `200px` | `240px` |
| `--sw-sun-top` | `-48px` | `auto` |
| `--sw-sun-left` | `-48px` | `auto` |
| `--sw-sun-bottom` | `auto` | `-48px` |
| `--sw-sun-right` | `auto` | `-48px` |
| `--sw-tab-active` | `#141210` | `#E8E2F2` |
| `--sw-tab-inactive` | `rgba(20,18,16,.38)` | `rgba(232,226,242,.32)` |
| `--sw-tab-line` | `#C97D0A` | `#F0700A` |

### Figma variable → CSS token mapping

Figma file: **Sidewalk Studio — Design System**

| Figma Collection | Figma Variable | CSS Token |
|---|---|---|
| `Tokens/Theme` | `bg/base` | `--sw-bg-base` |
| `Tokens/Theme` | `bg/surface` | `--sw-bg-surface` |
| `Tokens/Theme` | `bg/grid` | `--sw-bg-grid` |
| `Tokens/Theme` | `text/primary` | `--sw-text-primary` |
| `Tokens/Theme` | `text/secondary` | `--sw-text-secondary` |
| `Tokens/Theme` | `text/muted` | `--sw-text-muted` |
| `Tokens/Theme` | `accent/dominant` | `--sw-accent-dominant` |
| `Tokens/Theme` | `accent/green` | `--sw-accent-green` |
| `Tokens/Theme` | `accent/sun` | `--sw-accent-sun` |
| `Tokens/Theme` | `accent/coral` | `--sw-accent-coral` |
| `Tokens/Theme` | `border/default` | `--sw-border` |
| `Tokens/Theme` | `border/focus` | `--sw-border-focus` |
| `Tokens/Shared` | `font/display` | `--sw-font-display` |
| `Tokens/Shared` | `font/heading` | `--sw-font-heading` |
| `Tokens/Shared` | `font/body` | `--sw-font-body` |
| `Tokens/Shared` | `font/code` | `--sw-font-code` |
| `Tokens/Shared` | `radius/sm` | `--sw-radius-sm` |
| `Tokens/Shared` | `radius/md` | `--sw-radius-md` |
| `Tokens/Shared` | `radius/lg` | `--sw-radius-lg` |
| `Tokens/Shared` | `space/sm` | `--sw-space-sm` |
| `Tokens/Shared` | `space/md` | `--sw-space-md` |
| `Tokens/Shared` | `space/lg` | `--sw-space-lg` |
| `Tokens/Shared` | `space/xl` | `--sw-space-xl` |

### Token transformation system

None. Raw CSS → direct consumption. When a Figma variable changes, update `tokens.css` manually. The table above is the sync contract.

---

## 2. Component Library

### Component directory

```
resources/js/components/
  design-system/      ← atomic, theme-agnostic, no business logic
    SunAnchor.vue       signature atmospheric component
    AmbientGrid.vue     decorative column-grid overlay
    LegendChip.vue      categorical label with accent border
    MetricStrip.vue     horizontal KPI band
    SectionIntro.vue    standardised section opener
    SectionDivider.vue  labelled horizontal rule
    TransitLine.vue     vertical civic timeline
  layout/             ← page shell
    AppHeader.vue       header + SunAnchor + ThemeToggle + NavTabs
    ThemeToggle.vue     morning/sunset segmented control
    NavTabs.vue         horizontal tab strip
    AppFooter.vue       footer + privacy area
  content/            ← data-driven content blocks
    ProjectCard.vue     case study card
    WritingCard.vue     article teaser (horizontal layout)
    LocalBlock.vue      civic/local section
    LocalFootnote.vue   location footnote bar
  ui/                 ← generic interactive elements
    Button.vue          primary / secondary / ghost variants
    Panel.vue           surface wrapper
    CodeBlock.vue       syntax block (DM Mono)
    PullQuote.vue       Fraunces editorial quote
  icons/              ← SVG icon wrappers (see section 5)
```

### Component architecture

**Vue 3 Composition API · `<script setup lang="ts">` · no Options API.**

```vue
<!-- resources/js/components/ui/Button.vue — canonical shape -->
<script setup lang="ts">
type Variant = 'primary' | 'secondary' | 'ghost'
type Size    = 'sm' | 'md'

const props = withDefaults(defineProps<{
  variant?: Variant
  size?: Size
  disabled?: boolean
  href?: string
}>(), { variant: 'primary', size: 'md', disabled: false })

const emit = defineEmits<{ click: [e: MouseEvent] }>()
const tag  = computed(() => props.href ? 'a' : 'button')
</script>

<template>
  <component
    :is="tag"
    :href="href"
    :disabled="disabled"
    class="btn"
    :class="[`btn--${variant}`, `btn--${size}`]"
    @click="emit('click', $event)"
  >
    <slot />
  </component>
</template>

<style scoped>
.btn {
  font-family: var(--sw-font-body);
  border-radius: var(--sw-radius-md);
  transition: all var(--sw-motion-smooth);
  cursor: pointer;
}
.btn--primary {
  background: var(--sw-accent-sun);
  color: var(--sw-text-inverse);
  border: none;
}
.btn--secondary {
  background: transparent;
  color: var(--sw-text-primary);
  border: 1px solid var(--sw-border);
}
.btn--ghost {
  background: transparent;
  color: var(--sw-accent-dominant);
  border: none;
  text-decoration: underline;
}
.btn--md { padding: 10px 20px; font-size: 14px; }
.btn--sm { padding: 6px 14px;  font-size: 12px; }
.btn:disabled { opacity: 0.45; pointer-events: none; }
</style>
```

### SunAnchor — special component, unique rules

```vue
<!-- resources/js/components/design-system/SunAnchor.vue -->
<!-- Position, size, gradient all come from CSS variables. No JS logic. -->
<template>
  <div class="sun-anchor" aria-hidden="true" />
</template>

<style scoped>
.sun-anchor {
  position: absolute;
  width: var(--sw-sun-size);
  height: var(--sw-sun-size);
  top: var(--sw-sun-top);
  left: var(--sw-sun-left);
  bottom: var(--sw-sun-bottom);
  right: var(--sw-sun-right);
  border-radius: var(--sw-radius-xl);
  background: var(--sw-sun-gradient);
  box-shadow: 0 0 80px var(--sw-sun-glow);
  pointer-events: none;
  z-index: 0;
  transition:
    top var(--sw-motion-sun),
    left var(--sw-motion-sun),
    bottom var(--sw-motion-sun),
    right var(--sw-motion-sun),
    background var(--sw-motion-sun),
    box-shadow var(--sw-motion-sun);
  animation: sw-sun-drift 14s ease-in-out infinite;
}
@keyframes sw-sun-drift {
  0%, 100% { transform: translate(0,0) scale(1); }
  40%       { transform: translate(8px,-6px) scale(1.04); }
  70%       { transform: translate(-4px,7px) scale(0.97); }
}
@media (prefers-reduced-motion: reduce) {
  .sun-anchor { animation: none; }
}
</style>
```

### Figma component set → Vue component mapping

| Figma Component Set | Vue file |
|---|---|
| `SunAnchor` | `design-system/SunAnchor.vue` |
| `Header` | `layout/AppHeader.vue` |
| `ThemeToggle` | `layout/ThemeToggle.vue` |
| `NavTabs` | `layout/NavTabs.vue` |
| `Footer` | `layout/AppFooter.vue` |
| `SectionIntro` | `design-system/SectionIntro.vue` |
| `ProjectCard` | `content/ProjectCard.vue` |
| `WritingCard` | `content/WritingCard.vue` |
| `LocalBlock` | `content/LocalBlock.vue` |
| `LegendChip` | `design-system/LegendChip.vue` |
| `MetricStrip` | `design-system/MetricStrip.vue` |
| `Button` | `ui/Button.vue` |
| `Panel` | `ui/Panel.vue` |
| `CodeBlock` | `ui/CodeBlock.vue` |
| `PullQuote` | `ui/PullQuote.vue` |

### Component documentation

No Storybook. Figma file `Sidewalk Studio — Design System` → page `04 · Components` is the visual reference. Each Vue file has a JSDoc block:

```ts
/**
 * LegendChip
 * Categorical label with a 3px left accent border.
 * Figma: Components / LegendChip · variants: color × theme
 *
 * @prop label  - displayed text (uppercase, Syne 700 9px)
 * @prop color  - left border color variant
 */
```

---

## 3. Frameworks & Libraries

### Stack

| Layer | Tech | Version |
|---|---|---|
| Backend | Laravel | 13.x |
| Language | PHP | 8.3+ (8.5 on Vercel) |
| Bridge | Inertia.js | 3.x |
| UI | Vue | 3.5.x |
| Types | TypeScript | 5.9.x |
| CSS | none — hand-authored on `--sw-*` tokens | — |
| Build | Vite (Rolldown) | 8.x |
| PHP tests | PHPUnit | 13.x |
| JS tests | none | — |
| DB (dev and prod) | SQLite | — |

### CSS entry point

There is no CSS framework and no framework config. `resources/css/app.css` is
the entry: it imports the fonts, then `base.css` (element normalisation inside
`@layer reset`), then `tokens.css`, `reset.css`, `typography.css`, `layout.css`
and `view-transitions.css` in that order.

Unlayered rules always beat layered ones regardless of specificity, so
everything after `base.css` overrides it without needing higher specificity.

### Vite config

```ts
// vite.config.ts — abridged; see the file for the wayfinder and analyze plugins
import { wayfinder } from '@laravel/vite-plugin-wayfinder'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import { defineConfig } from 'vite'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.ts'],
      ssr: 'resources/js/ssr.ts',
      refresh: true,
    }),
    vue(),
    wayfinder({ formVariants: true }),
  ],
})
```

`app.css` is imported from `resources/js/app.ts`, not listed as a Vite input.
The `@` alias comes from `tsconfig.app.json` paths.

### Inertia bootstrap

```ts
// resources/js/app.ts
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { useTheme } from '@/composables/useTheme'

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./pages/**/*.vue', { eager: true })
    return pages[`./pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    useTheme()
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
})
```

---

## 4. Asset Management

### Directory structure

```
public/
  images/
    og/default.jpg           1200×630 · JPEG · ≤ 200 KB
    projects/[slug]/
      cover.webp             1600×900 · WebP
      thumb.webp             800×450  · WebP
    avatars/ismael.webp      320×320  · WebP
resources/content/
  projects/[slug].md         case study markdown
  writing/[slug].md          article markdown
```

### Asset rules

- No images in the hero section — atmosphere is typography + SunAnchor only.
- Every `<img>` must have explicit `width`, `height`, and `alt`.
- WebP for all photography. JPEG for OG fallback only.
- Card thumbnail max: 800×450, under 80 KB.
- No `autoplay` video backgrounds.
- All third-party embeds (maps, YouTube) are gated behind consent. Use `<IframeWrapper>`, never a raw `<iframe>`.

### CDN

Google Fonts only (see section 3 — font preload). No asset CDN in v0.

```html
<!-- resources/views/app.blade.php — exact subset, no extra weights -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
  href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@1,9..144,300;1,9..144,400&family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400&display=swap"
  rel="stylesheet"
>
```

---

## 5. Icon System

### Location

```
resources/js/components/icons/
  IconArrowRight.vue
  IconArrowUpRight.vue
  IconGitHub.vue
  IconLinkedIn.vue
  IconMastodon.vue
  IconMap.vue
  IconClock.vue
  IconTag.vue
  IconSunMode.vue          ThemeToggle morning indicator
  IconMoonMode.vue         ThemeToggle sunset indicator
  IconChevronDown.vue
  IconRss.vue
  IconExternalLink.vue
```

### Naming convention

`Icon[PascalCase].vue` — always prefixed `Icon`. One component per icon. No emoji filenames. No index barrel file (import directly).

### Usage pattern

```vue
<script setup lang="ts">
import IconArrowRight from '@/components/icons/IconArrowRight.vue'
</script>

<template>
  <!-- icon inline, inherits parent color -->
  <IconArrowRight :size="16" />

  <!-- icon inside button — button needs aria-label -->
  <button aria-label="Open project">
    <IconArrowRight :size="14" color="var(--sw-accent-sun)" />
  </button>
</template>
```

### Icon component shape

```vue
<!-- Every icon follows this exact shape -->
<script setup lang="ts">
withDefaults(defineProps<{ size?: number; color?: string }>(), {
  size: 16,
  color: 'currentColor',
})
</script>
<template>
  <svg
    :width="size" :height="size"
    viewBox="0 0 16 16"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
    focusable="false"
  >
    <!-- paths here -->
  </svg>
</template>
```

### Icon rules

- `aria-hidden="true"` + `focusable="false"` on every SVG.
- All icons are monochrome — `currentColor` or explicit `color` prop.
- No icon font. No SVG sprite. No `<use xlink:href>`.

---

## 6. Styling Approach

### CSS methodology

**Scoped Vue `<style scoped>` + global utility classes.** No CSS Modules, no CSS-in-JS.

```
resources/css/
  app.css              ← entry: @import fonts, then every file below in order
  base.css             ← element normalisation inside @layer reset
  tokens.css           ← all --sw-* custom properties (NEVER edit hex elsewhere)
  reset.css            ← box-sizing, smoothing, body wash, motion preferences
  typography.css       ← .type-* scale and .prose-copy
  layout.css           ← .sw-container, .sw-section, breakpoint helpers
  view-transitions.css ← cross-page transition names
```

### Global styles (reset.css extract)

```css
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html {
  font-family: var(--sw-font-body);
  background-color: var(--sw-bg-base);
  color: var(--sw-text-primary);
  -webkit-font-smoothing: antialiased;
  /* Theme transition propagates from here */
  transition:
    background-color var(--sw-motion-sun),
    color var(--sw-motion-smooth);
}

body { min-height: 100dvh; }

/* AmbientGrid — decorative 12-column overlay */
#app::before {
  content: '';
  position: fixed;
  inset: 0;
  background-image: repeating-linear-gradient(
    90deg,
    var(--sw-grid-line) 0px,
    var(--sw-grid-line) 1px,
    transparent 1px,
    transparent calc(100% / 12)
  );
  pointer-events: none;
  z-index: 0;
}

:focus-visible {
  outline: 2px solid var(--sw-border-focus);
  outline-offset: 2px;
  border-radius: inherit;
}

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.1ms !important;
  }
}
```

### Typography utilities (typography.css extract)

```css
.type-display-xl {
  font-family: var(--sw-font-display);
  font-style: italic; font-weight: 300;
  font-size: clamp(52px, 6vw, 88px);
  letter-spacing: -0.02em; line-height: 1;
}
.type-display-l {
  font-family: var(--sw-font-display);
  font-style: italic; font-weight: 300;
  font-size: clamp(36px, 4vw, 56px);
  letter-spacing: -0.01em; line-height: 1.05;
}
h1, .type-h1 { font-family:var(--sw-font-body); font-weight:600; font-size:clamp(28px,3vw,40px); letter-spacing:-0.01em; line-height:1.15; }
h2, .type-h2 { font-family:var(--sw-font-body); font-weight:600; font-size:clamp(20px,2vw,28px); line-height:1.22; }
h3, .type-h3 { font-family:var(--sw-font-body); font-weight:500; font-size:18px; letter-spacing:0.005em; line-height:1.3; }

.type-body-lg { font-size:18px; line-height:1.65; }
p, .type-body  { font-size:16px; line-height:1.58; }
.type-body-sm  { font-size:14px; line-height:1.5; }

.type-eyebrow {
  font-family: var(--sw-font-heading); font-weight: 700;
  font-size: 10px; letter-spacing: 0.22em; text-transform: uppercase;
  color: var(--sw-accent-sun);
}
.type-nav {
  font-family: var(--sw-font-heading); font-weight: 700;
  font-size: 9px; letter-spacing: 0.14em; text-transform: uppercase;
}
.type-meta {
  font-family: var(--sw-font-body);
  font-size: 12px; letter-spacing: 0.01em;
  color: var(--sw-text-muted);
}
code, .type-code {
  font-family: var(--sw-font-code); font-size: 13px;
  background: var(--sw-bg-grid);
  padding: 1px 6px; border-radius: var(--sw-radius-sm);
}
```

### Typography family assignment — enforced

| Family | Allowed in | Forbidden in |
|---|---|---|
| Fraunces italic 300 | Display, hero title, pull quotes | Body, H1–H3, nav, labels, code |
| Syne 700/800 | Eyebrow, nav, tab, chip, badge | Body paragraphs, code |
| DM Sans 300–600 | All body text, H1–H3, UI labels, buttons | Display moments, code |
| DM Mono 400 | Code blocks, CLI, token values | Everything else |

### Responsive strategy

Mobile-first. Three breakpoints:

```css
/* layout.css */
.layout-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: var(--sw-space-sm);        /* 24px */
  max-width: 1200px;
  margin-inline: auto;
  padding-inline: 48px;
}
@media (max-width: 1024px) { .layout-grid { padding-inline: 24px; } }
@media (max-width: 640px) {
  .layout-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    padding-inline: 20px;
  }
}

.section-hero     { padding-block: var(--sw-space-2xl); }  /* 128px */
.section-standard { padding-block: var(--sw-space-xl); }   /* 96px  */
.section-dense    { padding-block: var(--sw-space-lg); }   /* 64px  */

@media (max-width: 640px) {
  .section-hero     { padding-block: 80px; }
  .section-standard { padding-block: 64px; }
  .section-dense    { padding-block: 48px; }
}
```

In Vue SFCs:
```vue
<style scoped>
.component { /* mobile default */ }
@media (min-width: 640px)  { .component { /* tablet  */ } }
@media (min-width: 1024px) { .component { /* desktop */ } }
</style>
```

---

## 7. Project Structure

### Repository map

```
sidewalk-studio/
├── app/
│   ├── Data/SiteSettings/         typed readonly DTOs (spec 009)
│   ├── Enums/
│   │   ├── SiteSettingGroup.php   6 groups: identity, contact, social, seo, consent, features
│   │   └── SiteSettingKey.php     23 keys, each knows its group
│   ├── Http/
│   │   ├── Controllers/           one controller per public page
│   │   └── Middleware/HandleInertiaRequests.php  → injects SharedProps
│   ├── Models/SiteSetting.php     group/key casts, mapForGroup(), setValue()
│   ├── Services/SiteSettingsService.php  cache TTL 1h, sharedPayload()
│   └── Support/Seo.php            metadata builder (reads SiteSettingsService)
│
├── database/
│   ├── migrations/..._create_site_settings_table.php
│   └── seeders/SiteSettingsSeeder.php   idempotent, safe to re-run
│
├── resources/
│   ├── css/
│   │   ├── tokens.css             ★ ONLY place to edit design token values
│   │   ├── typography.css
│   │   ├── layout.css
│   │   ├── reset.css
│   │   └── app.css                entry point
│   ├── js/
│   │   ├── app.ts                 Inertia + Vue bootstrap
│   │   ├── composables/
│   │   │   ├── useTheme.ts        system pref detection + localStorage
│   │   │   └── useSeo.ts          per-page metadata helper
│   │   ├── components/            see section 2
│   │   ├── pages/                 Inertia page components
│   │   │   ├── Home.vue
│   │   │   ├── About.vue
│   │   │   ├── Projects/{Index,Show}.vue
│   │   │   ├── Writing/{Index,Show}.vue
│   │   │   ├── Local.vue
│   │   │   └── Contact.vue
│   │   └── types/inertia.d.ts     SharedProps type definition
│   ├── views/app.blade.php        Inertia root (font preloads here)
│   └── content/
│       ├── projects/[slug].md
│       └── writing/[slug].md
│
├── specs/                         GitHub Spec Kit (file-based, Codex-compatible)
│   └── 009-admin-site-settings/
│       ├── spec.md
│       ├── plan.md
│       └── tasks.md
│
├── docs/
│   ├── architecture/
│   ├── seo/
│   ├── rgpd/
│   └── ai/
│
├── tests/
│   └── Feature/SiteSettings/SiteSettingsServiceTest.php
│
├── public/images/                 see section 4
├── vite.config.ts
├── tsconfig.json
└── CLAUDE.md                      ← this file
```

### Inertia SharedProps contract

```ts
// resources/js/types/inertia.d.ts
interface SharedProps {
  site: {
    name: string           // "Sidewalk Studio"
    tagline: string        // "Engineering. Privacy. Cities."
    locale: string         // "fr-FR"
    author: string         // "Ismael Rodmacq"
    seo: {
      titleSeparator: string
      defaultTitle: string
      defaultDescription: string
      ogImage: string
    }
    contact: { email: string; city: string; country: string }
    social: { github: string; linkedin: string; mastodon: string }
    features: {
      maintenanceMode: boolean
      analyticsEnabled: boolean
      mapEnabled: boolean
    }
  }
}
```

### Theme composable

```ts
// resources/js/composables/useTheme.ts
export type Theme = 'morning' | 'sunset'

export function useTheme() {
  const stored  = localStorage.getItem('sidewalk-theme') as Theme | null
  const system  = window.matchMedia('(prefers-color-scheme: dark)').matches
  const initial = stored ?? (system ? 'sunset' : 'morning')
  const current = ref<Theme>(initial)

  function set(theme: Theme) {
    current.value = theme
    document.documentElement.setAttribute('data-theme', theme)
    localStorage.setItem('sidewalk-theme', theme)
  }

  set(initial)
  return { current: readonly(current), set }
}
```

### Feature organization

Each product spec lives in `specs/[NNN]-[slug]/`. Corresponding PHP code in `app/` follows Laravel conventions. No feature folders (no `app/Features/`). Vue pages map 1:1 to routes — no nested page layouts beyond AppHeader + AppFooter.

---

## 8. Figma → Code Implementation Checklist

When translating a Figma component to Vue using MCP:

```
[ ] Look up the component in the Figma → Vue mapping table (section 2)
[ ] Check all fills reference Tokens/Theme variables, not raw hex
[ ] Map each Figma variable to its --sw-* CSS token (table in section 1)
[ ] Write typed defineProps + defineEmits in <script setup lang="ts">
[ ] Use semantic HTML elements in <template>
[ ] Use var(--sw-*) for every color in <style scoped> — zero hex
[ ] Apply .type-* classes for typography (section 6)
[ ] Apply --sw-space-* for all spacing values (section 1)
[ ] Apply --sw-radius-* for all border-radius (section 1)
[ ] Add @media blocks for tablet/desktop if layout changes
[ ] Add JSDoc block: component name, Figma path, props summary
[ ] Verify: grep '#[0-9A-Fa-f]{6}' ComponentName.vue → zero results
```

### Figma text style → CSS

| Figma | CSS |
|---|---|
| `Display/XL` | `.type-display-xl` |
| `Display/L` | `.type-display-l` |
| `Heading/H1` | `h1` or `.type-h1` |
| `Heading/H2` | `h2` or `.type-h2` |
| `Heading/H3` | `h3` or `.type-h3` |
| `Body/Large` | `.type-body-lg` |
| `Body/Default` | `p` or `.type-body` |
| `Body/Small` | `.type-body-sm` |
| `Label/Eyebrow` | `.type-eyebrow` |
| `Label/Nav` | `.type-nav` |
| `Label/Meta` | `.type-meta` |
| `Code/Inline` | `code` or `.type-code` |

### Figma spacing → CSS token

| Figma auto-layout gap | Token |
|---|---|
| 4 | `--sw-space-4xs` |
| 8 | `--sw-space-3xs` |
| 12 | `--sw-space-2xs` |
| 16 | `--sw-space-xs` |
| 24 | `--sw-space-sm` |
| 40 | `--sw-space-md` |
| 64 | `--sw-space-lg` |
| 96 | `--sw-space-xl` |
| 128 | `--sw-space-2xl` |

### Figma corner radius → CSS token

| Figma radius | Token |
|---|---|
| 0 | `--sw-radius-none` |
| 4 | `--sw-radius-sm` |
| 8 | `--sw-radius-md` |
| 16 | `--sw-radius-lg` |
| 9999 | `--sw-radius-xl` (SunAnchor) or `--sw-radius-full` (avatar) |

---

## 9. Validation Commands

```bash
# No hardcoded hex colors in components — expected: empty output
grep -rn '#[0-9A-Fa-f]\{6\}' resources/js/components/ --include="*.vue"

# PHP test suite (PHPUnit)
php artisan test

# PHP static analysis + CS
composer run ci:check

# TypeScript check
npm run types:check

# Production build
npm run build

# Route audit
php artisan route:list --columns=method,uri,name
```

---

## 10. Hard Rules

```
✕  Never add a fifth font family
✕  Never hardcode a hex value in a .vue file or in <style scoped>
✕  Never use border-radius > 16px on any card or panel
✕  Never copy the SunAnchor glow or gradient pattern to another component
✕  Never use Fraunces for body text, H1, H2, H3, or navigation
✕  Never use Syne for body paragraphs or long-form prose
✕  Never use DM Mono outside of code contexts
✕  Never add a third visual theme
✕  Never load a third-party script outside the consent registry
✕  Never use a raw <iframe> — always IframeWrapper
✕  Never store credentials or secrets in site_settings
✕  Never build admin UI before spec 010 is active
✕  Never use localStorage for any key other than 'sidewalk-theme'
```
