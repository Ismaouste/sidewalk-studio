---
name: sidewalk-spec-editor
description: Create or update Sidewalk Studio feature specs, plans, and tasks under the repo `specs/` directory. Use when Codex needs to add or revise a spec while keeping `linear_issue`, docs sync points, and the project constitution aligned.
---

Create or update one feature folder under `specs/<id>/`.

Always:
- keep `spec.md`, `plan.md`, and `tasks.md` together
- preserve the optional `linear_issue` field in `spec.md`
- cross-check `AGENTS.md` and `.specify/memory/constitution.md` before widening scope
- update the relevant docs when a spec changes content, consent, or SEO behavior
- mirror tracking changes in the files under `docs/ai/` when the spec status or mapping changed

Use concise Markdown and prefer implementation-safe acceptance criteria over vague product language.
