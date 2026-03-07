# Codex Handoff: Career Assets Integration

Use this brief only after the current runtime/content branch is stable and the worktree is clean enough to touch the app safely.

## Goal
Integrate the recruiter-facing assets prepared in `docs/career/` into the project with minimal risk and without undoing the current public-site work.

## Inputs
- `docs/career/cv-fr.md`
- `docs/career/cv-en.md`
- `docs/career/profile-fr.md`
- `docs/career/profile-en.md`
- `docs/career/site-copy-fr.md`
- `docs/career/cv-fr.html`
- `docs/career/cv-en.html`
- `docs/career/render-pdfs.mjs`

## Priority order
1. Review the current worktree and avoid touching any files that are still being changed by another agent.
2. If the public content architecture is ready, wire French page content as a future locale layer without enabling it publicly yet.
3. Add a downloadable CV surface only if it can be done cleanly:
   - either as static downloadable files under a public-safe path
   - or as links from a future `Experience` or `Contact` area
4. Generate PDFs from the HTML sources if Playwright or an equivalent local print path is available.
5. Keep English as the default live language until a locale-detection and translation strategy is implemented properly.

## Constraints
- Do not rewrite the design system.
- Do not start full i18n routing unless the app is ready for it.
- Do not replace the current English public copy blindly.
- Do not invent chronology or employers beyond what the prepared assets already state.
- If any detail is uncertain, prefer a truthful generic label such as `Recent years` rather than an inaccurate exact claim.

## Good next tasks
- Add `resources/content/pages/fr/` equivalents for `home`, `experience`, and `local` from `docs/career/site-copy-fr.md`.
- Add a small `docs/career` README link from the root README if relevant.
- Add CV download links from the public site once PDF export succeeds.
- If locale detection is later implemented, default to `fr` for French browsers and `en` otherwise, with explicit user override.

## Validation
If runtime code is touched:
- `git diff --check`
- `npm run types:check`
- `npm run build`
- `php artisan test`
- `composer run ci:check`

If only docs/career assets are used, validate with:
- `git diff --check`

## Commit suggestions
- `docs(career): add recruiter-ready FR and EN assets`
- `feat(content): prepare french page copy sources`
- `feat(career): add printable CV downloads`
