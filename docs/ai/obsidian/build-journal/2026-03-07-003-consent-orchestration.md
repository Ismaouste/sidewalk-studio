# 2026-03-07 Build Log

## Summary

The consent layer was delivered with fixed v0 categories and media gating while keeping analytics on the `none` driver.

## Related tracking

- Spec: `specs/003-consent-orchestration/`
- Linear: `TODO`
- GitHub Project: `TODO`
- Release: `v0-foundation`

## Decisions

- Keep only `necessary`, `analytics`, and `media` for v0.
- Keep future adapters behind a small internal registry.

## Validation

- `php artisan test`
- `composer run ci:check`

## Follow-up

- Replace `TODO` values once the first real tracking items exist.
