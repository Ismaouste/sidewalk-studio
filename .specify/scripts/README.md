# Reserved Spec Kit Script Location

GitHub Spec Kit commonly generates helper scripts in this directory when a repository is bootstrapped through the official `specify` CLI.

This repo keeps the directory so the layout stays recognizable and future-friendly, but it does not claim that the current Codex environment exposes native `/speckit.*` commands or auto-generated script hooks.

If the project is later initialized or refreshed with `specify init --here --ai codex`, reconcile the generated scripts with the repo rules in `AGENTS.md` before using them as the default workflow.
