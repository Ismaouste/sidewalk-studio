# Release Tracking Checklist

Use this file when the branch is ready for a real release-oriented checkpoint and the repo needs to be aligned with Linear and GitHub Project manually.

## Preconditions

- the branch is clean
- `README.md`, `Roadmap.md`, and `CHANGELOG.md` reflect the current shipped scope
- validation is current for the type of changes on the branch
- you know which spec rows are being closed now and which work is still only release-noted

## Current v0 checkpoint

For the current `codex/release-v0-foundation` line:

- specs ready to close as `validated`: `001`, `002`, `003`, `004`
- spec still deferred/proposed: `009`
- additional shipped scope currently represented in release notes and commits:
  - token-based theme system
  - public design-system primitives and shell
  - public page refinement including `Experience` and `Local`
  - `site_settings` read-side integration

If you want that later work to become first-class board scope, add dedicated specs first.

## Linear completion

Use:

- `docs/ai/linear/issue-bootstrap.md`
- `docs/ai/linear/v0-foundation-issues.md`
- `docs/ai/linear/spec-issue-map.md`

Manual steps:

1. Create or verify the Linear issues for `001` to `004`.
2. Copy the real issue keys into `specs/<id>/spec.md`.
3. Mirror the same keys into `docs/ai/linear/spec-issue-map.md`.
4. Leave `009` as `TODO` until a real issue exists.

## GitHub Project completion

Use:

- `docs/ai/github-project/field-schema.md`
- `docs/ai/github-project/v0-foundation-field-values.md`
- `docs/ai/github-project/roadmap-spec-issue-map.md`

Manual steps:

1. Create or verify one row for each shipped spec `001` to `004`.
2. Use `validated` as the status for those rows.
3. Backfill the real project item reference into `specs/<id>/spec.md`.
4. Mirror the same reference into both tracking map files.
5. Keep `009-admin-site-settings` as `proposed` until a real admin write milestone starts.

## Repo backfill order

1. Update the spec frontmatter.
2. Update `docs/ai/linear/spec-issue-map.md`.
3. Update `docs/ai/github-project/roadmap-spec-issue-map.md`.
4. Confirm `CHANGELOG.md` still describes the shipped scope.
5. Run `git diff --check`.

## Copy-paste references

- Release name: `v0-foundation`
- Tracked validated specs: `001-repo-bootstrap`, `002-content-system`, `003-consent-orchestration`, `004-seo-foundation`
- Proposed later spec: `009-admin-site-settings`

## Stop conditions

Stop and add or revise specs before marking tracking complete if:

- a board row needs to represent work that does not map cleanly to an existing spec
- the changelog and README materially disagree about shipped scope
- a real external ID exists in one place but not in the spec frontmatter and map files
