# Changelog

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
