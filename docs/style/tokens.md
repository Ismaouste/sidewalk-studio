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

The temporary compatibility aliases used during the migration have been removed.
Public code should read `--sw-*` tokens directly.
