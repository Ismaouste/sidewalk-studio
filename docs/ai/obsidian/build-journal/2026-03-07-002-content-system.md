# 2026-03-07 Build Log

## Summary

The repo now ships a Markdown-driven writing and case-study system with explicit frontmatter validation and public routes.

## Related tracking

- Spec: `specs/002-content-system/`
- Linear: `TODO`
- GitHub Project: `TODO`
- Release: `v0-foundation`

## Decisions

- Keep content versioned in the repo filesystem.
- Use one content source for rendering, routing, metadata, and sitemap behavior.

## Validation

- `php artisan test`
- `composer run ci:check`

## Follow-up

- Replace `TODO` values once the first real tracking items exist.
