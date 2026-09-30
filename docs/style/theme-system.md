# Theme System

One visual system, two mirrored themes (specs/018-site-refresh):

- `morning` — **paper**: ink on paper. The default.
- `sunset` — **night**: clear on black.

The identifiers are historical and are kept so that no type, test or loader
quote changes; what they mean is defined in `resources/css/tokens.css`. Both
themes carry the same roles (paper, ink, five greys, `--sw-mark`, `--sw-path`,
`--sw-here`), so a change made to one is a change made to both.

Implementation contract:

- theme selection lives on `<html data-theme="morning|sunset">`
- `resources/css/tokens.css` defines the values of each theme
- `resources/views/app.blade.php` applies the initial theme before the app boots
- `resources/js/composables/useTheme.ts` keeps the runtime state in sync

Default behavior:

- first load follows `prefers-color-scheme`; light when the system has no preference
- manual overrides are stored in `localStorage` under `sidewalk-theme`
- no inline colour injection from JavaScript

Every visual change is checked in both themes: text at 4.5:1 or better,
focus visible, and the three primaries used only in their role.
