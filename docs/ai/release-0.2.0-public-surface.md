# Public Surface Refresh 0.2.0

## Scope delivered

- Reframed the public site around a clearer recruiting and portfolio surface: `Hello`, `Expériences`, `Journal`, and `Contact`.
- Rewrote the French-first positioning, then aligned the English copy so the site speaks more directly about ecommerce delivery, product data, tracking, CMS work, and technical SEO.
- Added a static preview export for GitHub Pages so the front-end can be shared publicly with the current art direction, transitions, and page structure.
- Introduced portable seeded public profile data, contact submission storage, and a lightweight admin inbox path for first-contact handling.

## Why these choices were made

- The site moved away from generic portfolio language and placeholder copy because the strongest signal is now the actual work: product data, connectors, catalog quality, tracking, consent, SEO, and fullstack delivery.
- The navigation was simplified so the public surface reads faster for recruiters and collaborators. `Expériences` became the main professional entrypoint, while `Journal` and notes now support that story instead of competing with it.
- The visual system was polished toward a calmer, more distinctive identity: lighter chrome, denser mobile behavior, a more atmospheric background, and theme palettes that feel intentional rather than decorative.
- GitHub Pages preview was kept static on purpose. The goal of this release is easy sharing of the front-end without pretending the preview is the full Laravel runtime.

## Validation completed

- `php artisan test`
- `npm run types:check`
- `npm run build`
- `php artisan route:list`

## Known limits

- The public preview on GitHub Pages is still a static export, so form handling, consent persistence, and admin features are not live there.
- Locale routing still relies on the current query-parameter approach and has not yet been migrated to `/fr/...` and `/en/...`.
- Accessibility controls for motion, theme-transition intensity, and alternate contrast/color modes are not shipped yet.

## Next block

- Add a footer accessibility control panel to disable ambient motion, reduce theme-transition effects, and expose alternate visual modes for public-sector and accessibility-sensitive contexts.
- Continue replacing remaining thin placeholder sections with profile-specific writing and publish selected draft notes/case studies from the new editorial backlog.
