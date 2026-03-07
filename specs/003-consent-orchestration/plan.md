# Implementation Plan: Consent Orchestration

## Summary

Initialize consent once at app boot, centralize script/embed registration, and keep analytics disabled while preserving the future adapter contract.

## Decisions

- Keep consent categories fixed in v0
- Use a registry boundary between UI state and integrations

## Main changes

- Add consent config and shared props
- Register media and analytics adapters through the internal registry
- Prove the media-gating path with a consent-blocked YouTube demo

## Docs and tracking sync

- Update RGPD docs if the category contract or gating model changes

## Validation

- `php artisan test`
- `composer run ci:check`
- `npm run build`
