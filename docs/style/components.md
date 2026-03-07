# Components

The public design system is now organized into three small layers under
`resources/js/components/`:

- `design-system/`: `SunAnchor`, `AmbientGrid`, `SectionIntro`, `SectionDivider`, `LegendChip`, `MetricStrip`
- `layout/`: `AppHeader`, `ThemeToggle`, `NavTabs`, `AppFooter`
- `ui/`: `Button`, `Panel`

Existing content-facing components still in use:

- `SeoMeta`
- `RichText`
- `MediaEmbed`
- `ConsentPreferencesButton`

Current shell composition:

- `SiteLayout` wraps `AmbientGrid`, `AppHeader`, page content, and `AppFooter`
- the header owns navigation and theme switching
- the footer keeps consent access centralized

Current migration coverage:

- homepage migrated to the new shell and primitives
- `About` migrated lightly
- `Projects` migrated lightly
- long-form writing and case-study pages still rely on the new shell plus legacy-compatible tokens

Component rules:

- keep public primitives presentational and reusable
- keep SEO and consent logic outside visual primitives
- use `Fraunces` only for display moments, `Syne` for labels/nav, `DM Sans` for body/UI, and `DM Mono` for code
