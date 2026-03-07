---
name: sidewalk-content-editor
description: Create or update Sidewalk Studio writing and case-study Markdown entries in `resources/content/`. Use when Codex needs to edit frontmatter, preserve slug stability, or keep SEO fields aligned with the Laravel content model.
---

Edit Markdown content under `resources/content/writing` or `resources/content/case-studies`.

Always:
- preserve the required shared frontmatter fields
- preserve case-study-only fields when editing `case-studies`
- keep `status` explicit (`draft` or `published`)
- avoid breaking stable slugs once a page is public
- update SEO fields when the title or summary changes materially

Do not change the frontmatter schema without also updating `docs/architecture/content-system.md` and `docs/seo/content-model.md`.
