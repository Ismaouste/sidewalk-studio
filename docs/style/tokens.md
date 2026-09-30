# Tokens

`resources/css/tokens.css` is the single source of truth for public `--sw-*`
tokens. `resources/css/base.css` holds base element normalisation inside
`@layer reset`. There is no CSS framework. Components read `--sw-*` variables
and never hard-code a colour, a font, a spacing or a motion value.

The map has three blocks: shared tokens (type, spacing, radius, motion), the
`morning` (paper) theme, and the `sunset` (night) theme, selected by
`html[data-theme]`.

Families:

- **Type**: `--sw-font-display|heading|body` (all Switzer) and `--sw-font-code`
  (DM Mono). Labels, legends and buttons use the mono voice in capitals.
- **Roles**: `--sw-paper`, `--sw-ink`, `--sw-grey-1` to `--sw-grey-5`, and the
  three primaries `--sw-mark` (yellow), `--sw-path` (blue), `--sw-here` (red).
- **Surfaces and text**: `--sw-bg-*`, `--sw-text-*`. Flat, opaque.
- **Old accent slots** (`--sw-accent-*`): kept by name and mapped onto the
  roles. `sun` maps to ink, because it was used as text and yellow on paper is
  1.6:1; `dominant` and `sky` and `violet` map to the path blue, `coral` to the
  "here" red. Prefer the role tokens in new code.
- **Lines**: `--sw-border`, `--sw-border-focus`, `--sw-hairline`,
  `--sw-hairline-strong`, `--sw-grid-line`.
- **Radius**: `--sw-radius-none` to `--sw-radius-full` stop at 2px; the round
  shape is `--sw-radius-pill`.
- **Space**: `--sw-space-4xs` to `--sw-space-3xl`. **Layout**: `--sw-container-*`,
  `--sw-shell-max-width`, `--sw-layout-gutter-*`, `--sw-header-offset`.
- **Motion**: `--sw-motion-fast|smooth|reveal|drift|flow`. Reduced motion sets
  them all to `0ms linear`.
- **The Stage**: `--sw-stain-*` (tones, opacities, blend mode).
- **Neutral legacy tokens** (`--sw-sun-*`, `--sw-ambient-*`, `--sw-body-wash`,
  `--sw-twilight-*`): kept so that nothing breaks, holding `none`, `transparent`
  or `0`. There is no sun, no flare and no wash any more.

`--sw-scrim` is the dimming behind a top-layer surface, mixed from the page
ground. The layout measures are the only tokens a component may read for
geometry; `--sw-header-offset` is what any sticky panel below the header measures
itself against.
