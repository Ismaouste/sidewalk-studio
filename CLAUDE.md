# CLAUDE.md

Personal portfolio site for Isma. Stack: Laravel 13 + Inertia 3 + Vue 3 + Vite 8 (Rolldown), deployed on Vercel. There is no CSS framework: all styling is hand-authored against `--sw-*` tokens. See `AGENTS.md` for the full repo contract; this file documents Claude-Code-specific conventions.

## Working rules

- Do not run `php artisan test`, `npm run build`, `npm run types:check`, or `vue-tsc` after every small change. Only before push, or when explicitly asked.
- All code, docs, and specs are English-only.
- Public content lives in FR/EN under `resources/content/pages/{fr,en}/<slug>.md` and must stay in shape parity (same frontmatter keys, same array lengths, same nested-object shape).
- Follow the GitHub Spec Kit workflow under `.specify/` and `specs/`. Use the `spec-kit-bootstrap` skill when starting a new feature.
- Never hardcode colors, fonts, spacing, or motion values in components — consume `--sw-*` tokens from `resources/css/tokens.css`.
- Two themes only: `morning` (light, architectural) and `sunset` (dark, violet glass). Both must be tested on visual changes.
- `sunset` carries no green and no amber: warm hues over a dark base collapse toward brown. Blurred surfaces saturate above 1, never below.
- Bilingual UI copy lives in `resources/js/copy/<locale>/<group>/<domain>.ts`, never inline in components. Each French module ends in `satisfies typeof import('../../en/<group>/<domain>').default`, and keys stay sorted (`sort-keys` enforces it).
- Prefer platform primitives over components: `popover`, `<details>`, `@layer`, `content-visibility`, scroll-driven animations, Inertia `prefetch`.

## Subagents and skills available in this repo

- Subagent `design-conformance-reviewer` — invoke after writing or editing Vue components or CSS to check token usage, font roles, motion conformance, and theme parity vs `docs/style/`.
- Subagent `i18n-parity-reviewer` — invoke after editing anything under `resources/content/pages/`.
- Skill `spec-kit-bootstrap` — scaffold a new feature spec from `.specify/templates/`.
- Skill `i18n-content-parity` — diff FR vs EN frontmatter shape on demand.

## Hooks active in this repo (see `.claude/settings.json`)

- PostToolUse → `node .claude/hooks/format-on-edit.mjs` formats `.vue/.ts/.tsx/.js/.css/.json` via Prettier on every Edit/Write/MultiEdit.
- PreToolUse → `node .claude/hooks/block-secrets.mjs` refuses Edit/Write/MultiEdit on `.env*`, `*credentials*`, `*.pem`, `*.key`, `id_rsa*`, `secrets.{json,yaml,yml,toml}`.

## Local toolchain

PHP and Composer are not on `PATH` in the Bash tool. Prepend the PHP directory
and call Composer through the phar:

```
export PATH="/c/Users/ismae/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe:$PATH"
php /c/Users/ismae/.local/bin/composer.phar <command>
```

If `composer install` fails with HTTP 400 on `codeload.github.com`, that is
unauthenticated GitHub rate limiting: export
`COMPOSER_AUTH` built from `gh auth token`. Do not fall back to
`--prefer-source` — `vlucas/phpdotenv` ships `tests/fixtures/env/nul.env`, and
`nul` is a reserved Windows device name that aborts the whole checkout.

## Validation baseline

- Everything at once: `npm run check` (lint + format + types) and `composer run lint:check`
- Backend: `php artisan test`
- Frontend: `npm run types:check && npm run build`
- Lint/format: `npm run lint:check && npm run format:check`
- Routing/content: `php artisan route:list`
- Perf: `npm run audit:lighthouse` and `npm run audit:lighthouse:mobile` (requires `php artisan serve` on `127.0.0.1:8088`)
- Bundle: `npm run audit:bundle` (writes `storage/app/vite-bundle-report.html`)

## Required sync points

When changing the content model, update:
- `docs/architecture/sanity-content-strategy.md` (if relevant)
- Both `resources/content/pages/{fr,en}/<slug>.md`
- Run i18n parity check before merging

When changing visual primitives or theme behavior, update:
- `docs/style/components.md`
- `docs/style/tokens.md`
- `resources/css/tokens.css`
- Test both `morning` and `sunset` themes

## Profile context

This site is the public artifact for Isma's tech-lead-to-CTO positioning (atypical profile combining engineering depth with cultural and creative sensibility). Design and copy should reinforce both signals — neither pure engineering portfolio nor pure design portfolio.
