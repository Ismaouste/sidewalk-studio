# Tasks: Admin Site Settings

## Implementation

- [ ] Define the bounded settings keys and first settings groups
- [ ] Design the `site_settings` persistence shape as a bounded aggregate
- [ ] Keep secrets and API keys out of `site_settings` and document that boundary in the module contract
- [ ] Add typed validation and the shared read service contract
- [ ] Define cache invalidation and refresh behavior for writes
- [ ] Define the idempotent default bootstrap or seed path from config and env-backed values
- [ ] Refactor public consumers so site-wide reads go through one settings service
- [ ] Define the write contract that `010-admin-shell-and-auth` can mount behind a protected UI

## Documentation

- [ ] Add the site-settings architecture doc
- [ ] Record the `.env` versus `site_settings` source-of-truth decision
- [ ] Update the relevant tracking docs and roadmap sequence

## Validation

- [ ] Add tests for default fallback, validation, cache invalidation, and shared public consumption
- [ ] Run the relevant validation commands
- [ ] Verify the local workflow still matches the docs

## Handoff

- [ ] Confirm the dependency handoff to `010-admin-shell-and-auth`
- [ ] Confirm later follow-ups for `011-admin-audit-log` and `012-installation-onboarding`
- [ ] Update release notes or changelog if the feature lands in a shipped milestone
