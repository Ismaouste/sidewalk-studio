# Career Assets

This folder groups recruiter-facing and profile-facing assets that are intentionally separated from the public app runtime.

## Core markdown assets
- `english-cv.md`: main English recruiter-ready CV
- `cv-fr.md`: main French recruiter-ready CV
- `positioning.md`: English positioning summary for interviews or profile pages
- `role-targets.md`: likely role targets and search framing
- `short-bio.md`: short English bio
- `profile-fr.md`: French headline, bios, recruiter summary, and interview angles
- `site-copy-fr.md`: French source copy for future `Home`, `Experience`, and `Local` localization work

## Printable assets
- `cv-fr.html`: printable French CV
- `cv-en.html`: printable English CV
- `cv.css`: shared printable styles
- `render-pdfs.mjs`: Playwright-based PDF export script
- `assets/avatar-github.jpg`: cached GitHub avatar used by the printable CVs
- `output/`: generated PDF destination

## Handoff
- `CODEX_NEXT_PROMPT.md`: brief for Codex CLI once the main worktree is stable enough to integrate these assets into the app

## Notes
- English remains the live public language for now.
- French copy here is a source layer for a later locale-aware content pass.
- The printable CVs are designed for PDF export, not for in-app rendering.
