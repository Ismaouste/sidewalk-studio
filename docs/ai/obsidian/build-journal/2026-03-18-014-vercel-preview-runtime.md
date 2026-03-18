# 2026-03-18 Build Log

## Summary

The repository now has a tracked Vercel preview runtime for the Laravel app, separate from the static GitHub Pages export.

## Related tracking

- Spec: `specs/014-vercel-preview-runtime/`
- Linear: `TODO`
- GitHub Project: `TODO`
- Release: `post-v0`

## Decisions

- Keep Vercel preview support versioned in the repo instead of rebuilding temporary staging folders by hand.
- Use a PHP entrypoint that moves writable Laravel state into temp storage before boot.
- Default the preview runtime to file-backed settings, cookie sessions, array cache, and SSR-disabled rendering.
- Keep the supported deployment path local-first: build locally, then deploy with the Vercel CLI.

## Validation

- local bootstrap check through `api/index.php`
- validation commands run from the repo baseline

## Follow-up

- Replace `TODO` tracking fields once a real Linear issue and GitHub Project item exist.
