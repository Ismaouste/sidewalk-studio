# Theme System

Sidewalk Studio uses one visual system with two atmospheric states:

- `Morning Grid` for light mode — vibrant civic primaries on chalk neutrals
- `Sunset Signal` for dark mode — electric violet and magenta on deep aubergine
  glass, with one cyan as the cool note

`Sunset Signal` deliberately carries no green and no amber. Warm hues mixed
over a dark base collapse toward brown, and blurred surfaces must saturate
above 1 (`--sw-surface-backdrop-filter`), never below it, or the glass greys
the palette out.

Implementation contract:

- theme selection lives on `<html data-theme="morning|sunset">`
- `resources/css/tokens.css` defines the theme-specific values
- `resources/views/app.blade.php` applies the initial theme before the app boots
- `resources/js/composables/useTheme.ts` keeps the runtime state in sync

Default behavior:

- first load follows `prefers-color-scheme`
- manual overrides are stored in `localStorage` under `sidewalk-theme`
- no inline color injection from JavaScript

Atmospheric rules already implemented:

- `SunAnchor` reads `--sw-sun-*` tokens directly
- the sun sits top-left in `Morning Grid`
- the sun sits bottom-right in `Sunset Signal`
- `--sw-body-wash` paints the page behind every translucent surface: two small
  circles in `Morning Grid`, three wide ellipses in `Sunset Signal` so the
  gradient still varies across a single glass panel

This keeps the theme system portable, SSR-friendly, and easy to extend without
introducing a heavier token pipeline.
