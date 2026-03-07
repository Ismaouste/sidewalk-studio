# Repo-local Skills

Repo-local skills are versioned under `tools/codex/skills/`.
They are not auto-installed into Codex.

## Sync approach

If you want them available as installed skills, copy or symlink the relevant folder into `$CODEX_HOME/skills`.

## Current limitation

The machine currently has no usable Python interpreter on PATH, so the initial skill folders were authored manually instead of being generated through the `skill-creator` helper script.
That should be revisited later if Python is added locally.
