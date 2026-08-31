# Changelog

## [Unreleased]

### Changed

- Moved the stack a full major generation forward: Laravel 12 to 13, Inertia 2 to 3 on both sides, Vite 7 to 8 (Rolldown replaces Rollup), ESLint 9 to 10, PHPUnit 11 to 13, and Wayfinder 0.1.11 to 0.1.21. TypeScript stays on 5.9: TypeScript 7 is the native Go compiler and no longer exposes the JavaScript compiler API that `vue-tsc` and `typescript-eslint` both drive.
- Removed Tailwind, which shipped in every build without a single utility class being used. Base element normalisation is now owned by `resources/css/base.css` inside `@layer reset`. The main stylesheet dropped from 65.2 kB to 27.3 kB and the client build from 3.1s to 1.6s.
- Retuned the `Sunset Signal` dark theme from a warm palette that read brown to electric violet and magenta on deep aubergine glass, with one cyan accent. No green and no amber remain in that theme, and blurred surfaces now saturate above 1 instead of below it.
- Moved every bilingual UI string out of components into `resources/js/copy/<locale>/<group>/`, where each French module is checked against its English counterpart at compile time and keys are kept sorted by lint.
- Wired the lint, format and Pint gates into CI, which previously ran only type checks, the build and the test suite. CI now also runs a PHP 8.4 / 8.5 matrix so the version Vercel serves is exercised.
- Moved `laravel/tinker` to `require-dev` and build-only npm packages to `devDependencies`.

### Fixed

- The boost-contrast accessibility mode was unreachable: its composable ignored its own argument and always wrote `default`, while the panel advertised the control as "Soon". Its tokens were already fully authored.
- The static preview export silently stopped rewriting URLs under Inertia 3, which moves the page payload from a `data-page` attribute into a `<script type="application/json">` element.
- `startViewTransition` rejections were never caught, surfacing an `InvalidStateError` in the console on interrupted navigations.
- Anchor links landed underneath the sticky header for want of `scroll-padding-top`.

### Removed

- `.codex-tmp/`, a full 11 MB duplicate of the project including `vendor/` and `public/build/`, committed by accident and holding stale copies of every source file.
- Dead dependencies with no importer: `@vueuse/core`, `clsx`, `tailwind-merge`, `class-variance-authority`, and the `lib/utils.ts` helper that was their only consumer.
- `docs/ai/blockers.md`, whose only entry (GitHub CLI unreachable) is no longer true, and `docs/career/CODEX_NEXT_PROMPT.md`, a superseded handoff.

### Added

- Hover prefetching on primary navigation and content links, and `content-visibility: auto` on below-the-fold sections.
- Repo-owned Vercel preview runtime support through `api/index.php`, `vercel.json`, and `.vercelignore` for more faithful Laravel previews than the static export alone.
- Architecture and tracking docs for the supported Vercel preview workflow, including its temp-storage bootstrap behavior and local CLI deployment constraints.
- Clarified that GitHub Pages remains the static approximation while Vercel preview is the runtime-oriented preview path.

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
- Replaced several placeholder sections with profile-specific copy focused on product data, ecommerce delivery, CMS/connectors, tracking, consent orchestration, structured data, and editorial systems.
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
