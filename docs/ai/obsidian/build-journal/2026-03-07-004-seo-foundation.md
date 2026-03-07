# 2026-03-07 Build Log

## Summary

The SEO foundation was delivered from Laravel with canonical metadata, JSON-LD, sitemap generation, and dynamic `robots.txt`.

## Related tracking

- Spec: `specs/004-seo-foundation/`
- Linear: `TODO`
- GitHub Project: `TODO`
- Release: `v0-foundation`

## Decisions

- Keep metadata generated server-side for the first response.
- Keep SSR prepared but not required as a runtime dependency.

## Validation

- `php artisan route:list`
- `composer run ci:check`
- `npm run build:ssr`

## Follow-up

- Replace `TODO` values once the first real tracking items exist.
