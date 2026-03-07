# Motion

Motion remains deliberately restrained and functional.

Current motion behaviors in code:

- subtle lift on buttons, cards, and navigation interactions
- theme transitions driven by token changes on `html[data-theme]`
- slow atmospheric drift on `SunAnchor`
- line reveal on active navigation tabs

Guardrails:

- no parallax
- no autoplay decorative surfaces
- no page-specific animation systems
- reduced-motion disables the sun drift and collapses transition timing globally

This is enough for the public shell foundation. A broader motion spec still
belongs to `005-theme-and-motion`.
