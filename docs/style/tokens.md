# Tokens

`resources/css/tokens.css` is the single source of truth for public `--sw-*` tokens.

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
- atmospheric shell tokens: `--sw-header-bg`, `--sw-sun-*`, `--sw-tab-*`

Temporary compatibility aliases remain in `tokens.css` for older public pages
that still read legacy variables. New work should use `--sw-*` only.
