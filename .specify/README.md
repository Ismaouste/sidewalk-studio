# GitHub Spec Kit Alignment

This repository uses GitHub Spec Kit as its official specification standard.

## What is aligned

- `.specify/memory/constitution.md` is the governing constitution artifact.
- `.specify/templates/` stores the reusable spec, plan, task, decision, build-log, and release-note templates.
- `specs/<id>/` stores the active feature packages for this repo.
- The repo follows the Spec Kit phase names: constitution, specify, plan, tasks, and implement.

## Current Codex rule

Codex is the current execution workflow for this repository.
That means day-to-day work happens through `AGENTS.md`, the repo-local docs, and direct file edits.
Do not claim native `/speckit.*` command support in Codex unless those commands are actually available in the running environment.

## Scripts

GitHub Spec Kit commonly bootstraps helper scripts into `.specify/scripts/`.
This repo reserves that location, but does not pretend those scripts are currently generated or wired into Codex.
