# Vercel Preview Runtime

## Purpose

The repository now includes a versioned Vercel preview runtime for the Laravel application itself.
It exists alongside the GitHub Pages static export and is meant for a more faithful preview of the public site when a local CLI deployment is acceptable.

## Supported deployment shape

- Build assets locally with `npm run build` before deploying.
- Deploy the prepared workspace with `npx vercel deploy`.
- Keep `database/database.sqlite` present locally if the preview needs the current SQLite-backed admin or form state.

The supported path is a local CLI preview, not a Git-driven production deployment pipeline.
That keeps the workflow aligned with the repo's local-first posture and avoids pretending that production automation is already solved.

## Runtime model

- `vercel.json` serves `public/` as the static surface and rewrites application requests to `api/index.php` through `vercel-php@0.9.0`.
- `api/index.php` sets preview-safe defaults before Laravel boots:
  - file-backed site settings
  - SSR disabled by default
  - cookie sessions
  - array cache
  - `/tmp` storage, compiled views, and Laravel cache manifests
- When SQLite is the active connection and `DB_DATABASE` is not already defined, the entrypoint copies the local `database/database.sqlite` file into a writable temp location.

## Constraints

- This preview runtime is ephemeral and should not be treated as durable production infrastructure.
- Public browser assets must exist in `public/build` before deployment, while `public/index.php` stays excluded from the uploaded static surface.
- The preview is suitable for public rendering and realistic route checks, but long-lived state should still move to a proper external database before any real production launch.
- GitHub Pages remains the intentionally static public approximation; Vercel preview is the more faithful runtime path.

## Files

- Runtime entrypoint: `api/index.php`
- Project config: `vercel.json`
- Upload exclusions: `.vercelignore`
