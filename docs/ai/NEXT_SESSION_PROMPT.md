# Resume prompt

Paste the block below to start the next session. It repeats things you already
know, because it is written to be handed to an agent cold.

For the editorial back-office work specifically, use
`docs/ai/AUDIT_PROMPT_EDITORIAL.md` instead — that one opens an audit and ends
in a spec package, not in code.

---

## Where things stand

PR #19 is **merged** into `main` (merge commit `09eb934`, 18 commits) and
Vercel production is deployed. CI is green on the PHP 8.4 / 8.5 matrix — it had
been red on `main` for three runs before that PR.

Shipped there: the stack up a major generation (Laravel 13, Inertia 3, Vite 8
with Rolldown, ESLint 10, PHPUnit 13), Tailwind removed, bilingual UI copy moved
into `resources/js/copy/`, NavTabs and AccessibilityPanel rebuilt on the Popover
API, BreadcrumbTrail on a `view-timeline`, page transitions handed back to
Inertia, and two local-memory features (`specs/015-local-memory`).

PR #20 is **open, not merged**: the palette rework and the breadcrumb fixes on
`feat/light-blue-and-lit-grid`. Look at it in a browser before deciding.

## Baseline that must stay green

`npm run check`, `composer run lint:check`, `php artisan test`
(**85 tests / 756 assertions**), `npm run build:ssr`. Both themes get checked in
a browser on any visual change.

## Toolchain

PHP and Composer are not on the Bash tool's PATH:

```
export PATH="/c/Users/ismae/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe:$PATH"
php /c/Users/ismae/.local/bin/composer.phar <cmd>
```

`npm run dev` needs PHP on PATH too, for the Wayfinder plugin. If
`composer install` returns HTTP 400, that is unauthenticated GitHub rate
limiting — export `COMPOSER_AUTH` from `gh auth token`. Never fall back to
`--prefer-source`: `vlucas/phpdotenv` ships `tests/fixtures/env/nul.env`, and
`nul` is a reserved Windows device name that aborts the whole checkout.

**Never use `perl -i` without a backup suffix on Windows.** It destroys the file
and leaves a randomly-named temporary beside it. It cost `tokens.css` in the
session that wrote this. Use the Edit tool.

## Open work

### 1. Finish the visual rework (PR #20)

Still to do on that branch: run the `design-conformance-reviewer` subagent,
check contrast on the _new_ light-theme neutrals (they moved), test mobile, and
sync `docs/style/tokens.md` and `docs/style/theme-system.md` — a required sync
point in CLAUDE.md.

### 2. Editorial back-office and an agnostic site

Scoped in `docs/architecture/configurability-inventory.md`, and opened by
`docs/ai/AUDIT_PROMPT_EDITORIAL.md`. The knot is the Markdown-versus-database
precedence: repositories deliberately prefer Markdown over database rows, with
tests pinning it, so the admin can already edit publications the public site
ignores. Every editorial feature is blocked behind that decision.

## Known debts, to take when they cross the path

- `useAccessibilityPreferences` never seeds `data-motion` from
  `prefers-reduced-motion`, so the site's own switch and the media query
  disagree for anyone who set neither. This is the root fix behind several
  reduced-motion inconsistencies.
- `--sw-button-primary-bg` on `--sw-button-primary-text` measures **2.95:1** in
  the light theme — near-white on orange. Pre-existing, and it is the most
  visible button on the site.
- A link with `prefetch="hover"` and `cache-for` runs **two** full visit cycles
  when a pointer rests on it, so it crossfades twice. A prefetch-policy call.
- `--sw-tab-line` and `--sw-header-bg` have no consumers;
  `html[data-scroll-lock]` in `reset.css` has no writer.
- `actions/checkout@v4` and `actions/setup-node@v4` are forced onto Node 24 —
  move to `@v5`.
- ContentVisual SVGs are served `max-age=3600`, so a palette change takes up to
  an hour to reach returning visitors. **Immediately relevant** while PR #20 is
  in flight.

## Conventions

- English for all code, docs and specs. Conversation can be French.
- Never hardcode colours, fonts, spacing or motion: `--sw-*` tokens.
- `sunset` carries no green and no amber; blurred surfaces saturate above 1.
- No CSS anchor positioning: tried and reverted in `db61a48` because Chromium
  placed popups wrong and never fired `position-try-fallbacks`.
- One branch per body of work even when the diff grows; keep the commits atomic
  instead.
- Subagents: `design-conformance-reviewer` after any Vue component or CSS,
  `i18n-parity-reviewer` after anything under `resources/content/pages/`.
