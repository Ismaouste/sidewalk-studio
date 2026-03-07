# 2026-03-07 Build Log

## Summary

A draft spec package now exists for a bounded `site_settings` domain that can support future admin editing without introducing a full CMS.

## Related tracking

- Spec: `specs/009-admin-site-settings/`
- Linear: `TODO`
- GitHub Project: `TODO`
- Release: `post-v0`

## Decisions

- Keep SQLite as the first persistence layer if the feature is implemented.
- Keep `.env` for secrets and infrastructure while using `site_settings` for bounded non-secret runtime configuration.
- Keep API keys and other secrets out of `site_settings`; a separate encrypted store would be needed if that requirement appears later.
- Limit the scope to a small typed settings domain and leave the protected admin shell to a later dedicated feature.

## Validation

- spec package drafted
- tracking maps updated

## Follow-up

- Replace `TODO` values once real tracking items exist.
