# Changelog

## [0.2.0] - 2026-03-08

### Added

- Public static preview export for GitHub Pages, keeping the portfolio shell, theme system, loader, and front-end interactions available without the Laravel runtime.
- Seeded site settings snapshot and portable admin bootstrap so the public profile data can be recreated consistently on another machine.
- Contact submission persistence with a lightweight back-office inbox and a public data-processing page wired into the footer.
- Reusable publication widgets, content visuals, placeholder generation, draft support, and richer writing/case-study scaffolding for notes, journal entries, and references.
- Refined public editorial surface across French and English pages, including updated CV sources and regenerated career PDFs.

### Changed

- Reworked the public navigation around `Hello`, `Expériences`, `Journal`, and `Contact`, with a denser mobile menu, sticky breadcrumb behavior, and a footer-driven language/theme/consent control area.
- Consolidated the professional narrative around a single `Expériences` surface, clarified the Jewely / Flippad trajectory, separated associative work, and made public writing and notes serve the portfolio instead of feeling like detached archives.
- Replaced several placeholder sections with profile-specific copy focused on product data, e-commerce delivery, CMS/connectors, tracking, consent orchestration, structured data, and editorial systems.
- Polished the visual system with a calmer `Morning` accent, a less Halloween-like `Sunset` palette, a persistent ambient background, a lighter transition overlay, and a more stable loader treatment.
- Tightened the header, footer, cards, dividers, and content rhythm across desktop and mobile to reduce visual noise and keep the reading flow closer to the final public intent.

### Validation

- `php artisan test`
- `npm run types:check`
- `npm run build`
- `php artisan route:list`

### Deferred

- Footer accessibility controls to disable ambient/background motion, reduce theme-transition effects, and offer additional visual-accessibility modes such as low-contrast / color-vision-friendly rendering.
- Route migration from `?lang=` to dedicated locale paths such as `/fr/...` and `/en/...`.
- Dedicated front-end audit tooling for ongoing Core Web Vitals, cache, and request-performance tracking beyond the current manual optimization pass.

## [0.1.0] - 2026-03-07

### Added

- Laravel 12 + Inertia + Vue 3 + TypeScript application foundation for the public portfolio shell.
- Token-based `Morning Grid` / `Sunset Signal` theme system and reusable public design-system primitives.
- Public editorial pages for `Home`, `Experience`, `Local`, `Projects`, `Writing`, `Case Studies`, `Contact`, and `Labs`.
- Markdown-driven writing and case-study publishing with validated frontmatter and stable routes.
- Consent orchestration for `necessary`, `analytics`, and `media`, with analytics kept on the `none` driver.
- SEO foundations including canonical metadata, JSON-LD, `robots.txt`, and `sitemap.xml`.
- `site_settings` read-side foundation for non-secret public site values, seeded defaults, and shared Inertia/SEO reads.
- Spec-driven docs, roadmap, AI workflow references, and repo-local skills.

### Changed

- Normalized repository metadata and ignore rules for the Windows local-first workflow.
- Aligned the PHP content contract with the documented `updated_at` requirement.
- Refined the homepage into a clearer landing page and moved detailed professional/civic context into `Experience` and `Local`.
- Kept the SSR entrypoint ready without making SSR a required local runtime.
- Prepared the repo for manual Linear and GitHub Project completion without claiming live external integration.

### Deferred

- Additional motion polish and later visual-system refinement on top of the shipped theme/design-system base.
- Richer editorial case-study depth beyond the current public structure.
- CI/CD and deployment automation.
- Any server database migration; PostgreSQL is the preferred first option once a concrete product need exists.
- Real analytics adapters and external tooling integrations.
