# Tokens

`resources/css/tokens.css` is the single source of truth for public `--sw-*` tokens.

`resources/css/base.css` sits beside it and holds base element
normalisation inside `@layer reset`. There is no CSS framework; unlayered rules
always beat layered ones, so every other stylesheet overrides `base.css`
without needing higher specificity.

The token map is split into three blocks:

- shared tokens for typography, spacing, radius, and motion
- `Morning Grid` theme tokens for the light state
- `Sunset Signal` theme tokens for the dark state

The active theme is selected with `html[data-theme='morning']` or
`html[data-theme='sunset']`. Vue components and scoped styles should consume
`--sw-*` variables directly instead of hardcoding hex values.

Current token families:

- typography: `--sw-font-display`, `--sw-font-heading`, `--sw-font-body`, `--sw-font-code`
- spacing: `--sw-space-4xs` through `--sw-space-3xl`
- radius: `--sw-radius-none` through `--sw-radius-full`
- motion: `--sw-motion-fast`, `--sw-motion-smooth`, `--sw-motion-reveal`, `--sw-motion-sun`
- surfaces and text: `--sw-bg-*`, `--sw-text-*`
- accents and chrome: `--sw-accent-*`, `--sw-border`, `--sw-grid-line`, `--sw-shadow-*`
- atmospheric shell tokens: `--sw-header-bg`, `--sw-body-wash`, `--sw-sun-*`, `--sw-tab-*`
- layout measures: `--sw-container-*`, `--sw-shell-max-width`, `--sw-layout-gutter-*`, `--sw-header-offset`, `--sw-admin-shell-max-width`, `--sw-admin-nav-width`
- glass and top layer: `--sw-surface-backdrop-filter`, `--sw-scrim`, `--sw-scrim-backdrop-filter`

Each theme defines `--sw-body-wash` as its full page background, so the two
themes can differ in the shape of the ambient gradient and not only its hue.

`--sw-surface-backdrop-filter` is the blur every translucent surface reads,
and each theme sets its own radius and saturation. Sunset saturates above 1 on
purpose: pushing saturation down is what dragged its accents toward brown.

`--sw-scrim` is the dimming behind a top-layer surface, mixed from
`--sw-bg-base` rather than from black, so it darkens the theme instead of
draining it — a black scrim over sunset's aubergine flattens the violet wash to
neutral. It is consumed from `::backdrop`, which inherits from its originating
element, so tokens resolve there normally.

The layout measures are the only tokens a component may read for geometry, and
the two admin ones are a pair. `--sw-admin-shell-max-width` is the width the
back-office is allowed to occupy and `--sw-admin-nav-width` is the side column
inside it, so widening the column alone would take that width from the editor
rather than from the margin. Both moved together when the navigation panel
proved too narrow to read: the shell from `1180px` to `1320px`, the column from
a flat `220px` to `clamp(240px, 19vw, 300px)`. On a 1440px screen that widens
the panel and the content column at the same time.

`--sw-header-offset` is what any sticky panel below the header measures itself
against. A panel that pins there must also cap its own height, or its overflow
lands below the fold where the page scroll cannot reach it — see
`AdminLayout.vue`, which subtracts the offset and the body's own padding from
`100dvh`.

The temporary compatibility aliases used during the migration have been removed.
Public code should read `--sw-*` tokens directly.
