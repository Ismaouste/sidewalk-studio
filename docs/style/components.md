# Components

The public design system is now organized into three small layers under
`resources/js/components/`:

- `design-system/`: `AmbientGrid` (the ruled grid), `SectionIntro`, `SectionDivider`, `LegendChip`, `MetricStrip`, `ContentMetaRow`, `InlineTermTooltip`
- `stage/`: `Stage` (flat drifting stains; mounted once at the app root, see `app.ts`)
- `schema/`: `Schema` (a drawing from data: lots, interfaces, dimensions, compass, scale, with a text equivalent), `HomePlan`, `planLayout.ts`
- `sheet/`: `SheetFrame` (corner marks and graduated ruler, mounted once at the root), `Cartouche` (the title block in the footer)
- `layout/`: `AppHeader`, `ThemeToggle`, `NavTabs`, `BreadcrumbTrail`, `AccessibilityPanel`, `LocaleSwitcher`, `AppFooter`
- `ui/`: `Button`, `Panel`

Existing content-facing components still in use:

- `SeoMeta`
- `RichText`
- `MediaEmbed`
- `ConsentPreferencesButton`

Current shell composition:

- `SiteLayout` wraps `AmbientGrid`, `AppHeader`, the loader overlay, the
  breadcrumb, page content, and `AppFooter` (which opens with the `Cartouche`).
  Pages wrap themselves in `SiteLayout`, so it is not persistent: anything that
  must survive navigation (`Stage`, `SheetFrame`) is mounted at the app root
- the header owns navigation and theme switching
- the footer keeps consent access centralized

Platform primitives the layout leans on instead of components:

- The mobile navigation sheet and the accessibility panel are `popover`
  elements. The browser owns opening, closing, light dismiss, Escape and the
  top layer, so neither needs a z-index, a backdrop element or an outside-click
  listener. Two things it does not give you: it will not close a popover when
  a link inside it navigates, and an open popover stays in the top layer
  whatever `position` says, so crossing a breakpoint has to close it. Both
  cases are handled in `NavTabs`.
- Above its breakpoint, the navigation panel keeps the `popover` attribute and
  simply renders as the tab row. The UA's `display: none` for a closed popover
  is a UA-origin style, so one author media query outranks it — no JavaScript
  decides which mode the nav is in.
- Scrims are `::backdrop`, not elements. `::backdrop` inherits from its
  originating element, so `--sw-scrim`, `--sw-scrim-backdrop-filter` and
  `--sw-motion-fast` all resolve inside it.
- Which navigation entry is active is resolved by `PublicLocale::navigation()`
  and read from props. The client does not parse URLs.
- `ArticleShowLayout` carries a read-progress rail on
  `animation-timeline: scroll(root block)`, so it costs no scroll listener and
  hides itself under `@supports not`.

Surfaces that depend on what the browser remembers:

- The journal's "new since your last visit" badge and the article's resume
  invitation both read `useLocalMemory`, and neither can be part of the
  server-rendered markup — everyone is served the same HTML, so both appear
  after mount. See `specs/015-local-memory`.
- The resume invitation sits in the flow rather than over the page, and never
  scrolls on its own. Accepting it jumps under a view transition, which
  crossfades between the two positions instead of dragging the reader through
  everything in between.

Current migration coverage:

- homepage migrated to the new shell and primitives
- `Experience` and `Local` now carry the detailed editorial/professional content
- `Projects` migrated lightly
- writing, case-study, contact, and labs pages now use the same shell and primitives
- `ContentMetaRow` is the shared metadata treatment for archive cards and long-form headers

Component rules:

- keep public primitives presentational and reusable
- keep SEO and consent logic outside visual primitives
- Switzer for text and titles, DM Mono in capitals for labels, legends and buttons
- no blur, gradient, glass or shadow; hairlines separate surfaces
- a drawing always has a text equivalent and is `aria-hidden`
- one primary per area of the screen, used for its role only (mark, path, here)
