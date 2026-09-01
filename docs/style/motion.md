# Motion

Motion remains deliberately restrained and functional.

Current motion behaviors in code:

- subtle lift on buttons, cards, and navigation interactions
- theme transitions driven by token changes on `html[data-theme]`
- slow atmospheric drift on `SunAnchor`
- surface pill behind the active navigation tab, scaled and faded in on
  `::before` — this replaced an earlier line reveal, whose `--sw-tab-line`
  token has since been removed
- popover open and close on the mobile navigation sheet and the accessibility
  panel: opacity and `translate` under `@starting-style`, with
  `transition-behavior: allow-discrete` on `display` and `overlay` so the
  closing frame is not skipped
- page transitions on Inertia visits, wrapped around the page swap by the
  framework rather than by this codebase
- read-progress rail on articles, scaled along
  `animation-timeline: scroll(root block)` — no listener, and hidden entirely
  under `@supports not` and under `prefers-reduced-motion`
- resuming a partly-read article jumps under a view transition, so the two
  scroll positions crossfade; reduced motion, in either of its two forms,
  takes the jump without it

State reported through the compositor rather than animated:

- `BreadcrumbTrail` reads a `view-timeline` named by a one-pixel sentinel in
  `SiteLayout` to know it has reached the header, and swaps its backdrop with
  `step-end`. Nothing moves; the animation only reports a scroll position, so
  the no-parallax guardrail below does not apply to it.

Guardrails:

- no parallax
- no autoplay decorative surfaces
- no page-specific animation systems
- reduced-motion disables the sun drift and collapses transition timing globally

Reduced motion arrives through two separate paths, and they do not behave
alike. `html[data-motion='reduced']`, the site's own switch, blanks
`animation` outright, so a scroll-driven state animation has to opt back in by
name — `BreadcrumbTrail` does. The `prefers-reduced-motion` media query
instead clamps `animation-duration`, which a progress-based timeline ignores
for range mapping, so those animations keep working untouched. Note also that
`useAccessibilityPreferences` never seeds `data-motion` from the media query:
a visitor whose system asks for reduced motion, and who has set no preference
here, gets `data-motion="full"` and only the media-query path.

This is enough for the public shell foundation. A broader motion spec still
belongs to `005-theme-and-motion`.
