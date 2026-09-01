# Contributing

Sidewalk Studio is one person's production site and, at the same time, a
reference implementation you can read, fork, and learn from. Contributions
that improve the machine are welcome; the words are not open for editing.

## What is open

- Bug fixes, accessibility improvements, and performance work on the code.
- Improvements to the declared content schema, consent orchestration, SEO
  plumbing, or the static/Vercel preview tooling.
- Documentation fixes under `docs/` (except `docs/career/`).

## What is not

- `resources/content/**` and `docs/career/**` are the owner's writing and
  identity — all rights reserved, no PRs.
- Design-direction changes (palette, fonts, themes) — the art direction in
  `docs/style/` is a deliberate, documented choice.

## Ground rules

- All code, docs, and content keys are English-only; public copy ships in
  French and English in strict shape parity (`DeclaredPageContentTest` and
  `LanguageFileParityTest` enforce it).
- Never hardcode colors, fonts, spacing, or motion — consume `--sw-*` tokens
  from `resources/css/tokens.css`, and test both `morning` and `sunset`
  themes on any visual change.
- Prefer platform primitives (`popover`, `<details>`, `@layer`,
  scroll-driven animations, Inertia prefetch) over component machinery.

## Running it

See the Quickstart in [README.md](README.md).

## Validation baseline

Run before proposing changes:

```powershell
npm run check          # lint + format + types
php artisan test
npm run build
```
