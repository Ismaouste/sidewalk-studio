# Tracking Backfill Checklist

Use this checklist when a spec moves from placeholder tracking to real Linear and GitHub Project references.

## Inputs required

- the spec ID, for example `001-repo-bootstrap`
- the real Linear issue key, for example `SIDE-12`
- the real GitHub Project item identifier or stable title
- the target release, for example `v0-foundation`
- the linked Obsidian mirror note path

## Backfill order

1. Update the spec frontmatter in `specs/<id>/spec.md`.
2. Update the matching row in `docs/ai/linear/spec-issue-map.md`.
3. Update the matching row in `docs/ai/github-project/roadmap-spec-issue-map.md`.
4. Update the linked Obsidian mirror note if the tracking header still contains placeholders.
5. Update the GitHub Project row so it mirrors the same values.

## Fields to backfill

- `linear_issue`
- `github_project_item`
- `github_project_status`
- `obsidian_note`
- `release`

## Placeholder rule

- Keep `TODO` only where a real external identifier does not exist yet.
- It is acceptable for reusable bootstrap docs such as `v0-foundation-issues.md` and `v0-foundation-field-values.md` to keep template placeholders.
- The spec frontmatter and map files should use real values as soon as the item exists.

## Final check

Search the spec and the two map files for the spec ID and confirm the same values appear in all three places.
