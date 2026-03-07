# Implementation Plan: SEO Foundation

## Summary

Generate SEO payloads in PHP, pass them to Blade and Inertia, and expose sitemap/robots endpoints directly from the app.

## Decisions

- Keep metadata server-side for the first request
- Keep SSR compatibility without making SSR mandatory in v0

## Main changes

- Add the SEO builder and shared payload shape
- Render metadata, JSON-LD, sitemap, and robots from Laravel
- Keep article metadata tied to the content source

## Docs and tracking sync

- Update SEO docs if metadata, routing, or structured-data behavior changes

## Validation

- `php artisan test`
- `composer run ci:check`
- `npm run build`
- `npm run build:ssr`
