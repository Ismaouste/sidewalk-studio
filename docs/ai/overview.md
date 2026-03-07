# AI Overview

The repo has a dedicated AI layer because Sidewalk Studio is also meant to be operated with Codex-like agents.

## Active entrypoints

- `.specify/README.md` for the repo-local GitHub Spec Kit alignment notes
- `AGENTS.md` for repo-wide behavior
- `docs/ai/spec-kit.md` for the spec workflow and template roles
- `docs/ai/` for human-readable workflow rules
- `docs/ai/project-tracking.md` for Linear, Obsidian, and GitHub Project handoff conventions
- `docs/ai/release-planning.md` for release-scope and release-note rules
- `tools/codex/skills/` for repo-local reusable skills

## Principle

Do not bury repo context in ad-hoc prompts.
Put stable knowledge in docs or skills so future sessions can reuse it without rediscovering everything.
