# Motion

Motion is allowed and can be rich, and it is bounded. Every animation uses only
`transform`, `opacity`, `stroke-dashoffset` or a motion path, so nothing
triggers layout or repaints a large area.

What moves:

- **The Stage**: flat stains that drift by `transform`, 60 to 120 seconds a leg,
  mounted once at the app root so a fast navigation does not restart them.
- **Schemas**: dashed interface lines whose dashes flow, arrowheads that travel
  along the line and turn with it (SMIL `animateMotion`), and a compass that
  turns with the scroll (`animation-timeline: scroll()`, slow rotation where it
  is not supported).
- **The sheet frame**: the red reading mark on the ruler follows the scroll.
- **Interface**: hover and focus colour changes, popovers and the mobile
  navigation under `@starting-style`, page transitions wrapped around the page
  swap by Inertia, the read-progress rail on articles.

State reported through the compositor rather than animated: `BreadcrumbTrail`
reads a `view-timeline` and swaps its ground to opaque paper when it reaches the
header. It is opaque from the start on a phone, so it is never see-through.

Guardrails:

- pause when the tab is hidden or the drawing is out of view (SMIL is paused by
  `IntersectionObserver`)
- everything stops and diagrams render still under `prefers-reduced-motion` and
  under the site's own `html[data-motion='reduced']`
- a motion that blocks input, or that flashes more than three times a second,
  is a bug
- the background adds at most 25 KB of JavaScript and draws nothing per frame in
  JavaScript: the stains are CSS
