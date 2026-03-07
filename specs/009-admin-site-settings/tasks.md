# Tasks: Admin Site Settings

## Implementation

- [x] Define the bounded settings keys and first settings groups
- [x] Design the `site_settings` persistence shape as a bounded aggregate
- [x] Keep secrets and API keys out of `site_settings` and document that boundary in the module contract
- [x] Add typed validation and the shared read service contract
- [x] Define cache invalidation and refresh behavior for writes
- [x] Define the idempotent default bootstrap or seed path from config and env-backed values
- [x] Refactor public consumers so site-wide reads go through one settings service
- [x] Define the write contract that `010-admin-shell-and-auth` can mount behind a protected UI

## Documentation

- [x] Add the site-settings architecture doc
- [x] Record the `.env` versus `site_settings` source-of-truth decision
- [x] Update the relevant tracking docs and roadmap sequence

## Validation

- [x] Add tests for default fallback, validation, cache invalidation, and shared public consumption
- [x] Run the relevant validation commands
- [x] Verify the local workflow still matches the docs

## Handoff

- [x] Confirm the dependency handoff to `010-admin-shell-and-auth`
- [ ] Confirm later follow-ups for `011-admin-audit-log` and `012-installation-onboarding`
- [ ] Update release notes or changelog if the feature lands in a shipped milestone
