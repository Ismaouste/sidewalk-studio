# GitHub Project Field Schema

Use this as the initial field contract for the first project board.

## Recommended fields

- `Status`
  Use a single-select field with: `proposed`, `active`, `validated`, `deferred`
- `Spec ID`
  Use a text field with values like `001-repo-bootstrap`
- `Linear Issue`
  Use a text field with values like `SIDE-12`
- `Obsidian Note`
  Use a text field with the repo mirror path under `docs/ai/obsidian/build-journal/`
- `Release`
  Use a single-select field with at least: `v0-foundation`, `post-v0`
- `Roadmap Block`
  Use a text field that mirrors `Roadmap.md`

## Working rule

- Update the spec frontmatter first.
- Mirror the same values into the GitHub Project row.
- Keep the repo map in `docs/ai/github-project/roadmap-spec-issue-map.md` aligned with the board.
- Use `docs/ai/github-project/v0-foundation-field-values.md` for the first exact row values.
