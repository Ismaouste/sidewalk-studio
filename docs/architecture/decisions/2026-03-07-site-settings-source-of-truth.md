# Site Settings Source of Truth

## Status

Accepted

## Context

The project needs a future-friendly place for non-secret runtime configuration, but it must not blur the boundary between deploy-time secrets, application defaults, and editorial content.

## Decision

Use a bounded `site_settings` domain for non-secret runtime configuration and keep `.env` for secrets, credentials, infrastructure, and other runtime-sensitive values.
Do not use `site_settings` as a general-purpose secret store; admin-managed secrets would require a separate encrypted design later.

Markdown content remains the source of truth for writing and case studies.

## Consequences

- Positive:
  Public site settings can change without turning `.env` into a content store.
- Positive:
  The future admin surface has one clear domain to manage.
- Negative:
  The app must maintain bootstrap defaults and a cache invalidation path.
- Deferred:
  Authentication, audit logs, and installation onboarding remain follow-up work.

## References

- Spec: `specs/009-admin-site-settings/spec.md`
- Plan: `specs/009-admin-site-settings/plan.md`
- Related docs: `docs/architecture/site-settings.md`
