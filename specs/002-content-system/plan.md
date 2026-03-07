# Implementation Plan: Content System

## Summary

Use versioned Markdown as the content source, validate it in PHP, and shape the public pages through Laravel and Inertia.

## Decisions

- Keep content in the repo filesystem
- Validate frontmatter before public rendering

## Main changes

- Add the writing and case-study directories and seed entries
- Implement the PHP repository and public routes
- Reuse the same content source for metadata and sitemap logic

## Docs and tracking sync

- Update content-system and SEO content-model docs if the schema changes

## Validation

- `php artisan test`
- `composer run ci:check`
- `npm run build`
