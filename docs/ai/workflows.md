# AI Workflows

## Feature work

1. Start from the constitution and the relevant spec folder.
2. Use the matching template from `.specify/templates/` if the spec package is missing or incomplete.
3. Implement the smallest useful slice.
4. Update docs if the architecture, content model, consent layer, or SEO rules changed.
5. Mirror status changes in the tracking files under `docs/ai/`.
6. Run `php artisan test`, `composer run ci:check`, and `npm run build` when relevant.

GitHub Spec Kit defines the artifact flow.
Codex executes it here through repo files and documented conventions, not by assuming native `/speckit.*` slash commands are available.

## Content work

1. Add or edit Markdown entries under `resources/content/`.
2. Keep frontmatter complete and explicit.
3. Update SEO or content-model docs only if the schema changes.

## Linear convention

There is no API integration in v0.
If a feature maps to a Linear ticket, store the key in the relevant spec frontmatter under `linear_issue:`.
Keep the cross-tool mapping tables in `docs/ai/project-tracking.md` and the files it references.

## Release work

1. Update `CHANGELOG.md` when shipped scope changes.
2. Use `.specify/templates/release-note-template.md` when drafting a PR or milestone note.
3. Keep release notes limited to what was actually validated locally.
