# Theme System

Sidewalk Studio uses one visual system with two atmospheric states:

- `Morning Grid` for light mode
- `Sunset Signal` for dark mode

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

This keeps the theme system portable, SSR-friendly, and easy to extend without
introducing a heavier token pipeline.
