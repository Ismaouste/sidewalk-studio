---
linear_issue: TODO
github_project_item: TODO
github_project_status: implemented
obsidian_note: TODO
release: post-v0
title: Local Memory
status: implemented
---

# Feature Specification: Local Memory

The site remembers two things about a returning reader — which journal entries
have appeared since they were last here, and how far they had read into an
article — entirely in their own browser. No cookie, no request, no identifier,
no server-side record.

## Problem

The site argues a position on privacy: analytics stays opt-in, embeds stay
blocked until consent, the contact form persists nothing. Today a visitor has
to take that on trust, because every page reads the same whoever is looking at
it. The claim is made in prose on `/data-processing` and nowhere else.

Meanwhile the journal is growing. A reader who came back after two months has
no way to see what is new without remembering what they had already read, and
a reader interrupted halfway through a long entry starts again from the top.

Both problems have the same shape: the site would be more useful if it
remembered something, and the usual way to remember something about a visitor
is the thing this site refuses to do.

## Desired outcome

Two features that are genuinely useful, and whose implementation is itself the
argument. A reader can open devtools, clear site data, and watch the memory
disappear — because it was only ever in `localStorage`. The network tab shows
no request carrying it, because there is none.

- Returning to the journal, entries published since the last visit are marked.
- Returning to a partly-read article, the reader is offered their position
  back rather than being moved without asking.
- Reading progress is visible while scrolling, and costs no JavaScript.

## In scope

- A `lastVisitAt` timestamp per browser, compared against `published_at`, which
  is already in the content frontmatter and already shipped to the client.
- A per-article scroll position, keyed by slug.
- A resume invitation the reader accepts or ignores; scrolling is never
  automatic.
- A scroll progress indicator driven by `animation-timeline: scroll()`.
- Bilingual copy for every string, in the copy tree.
- Graceful behaviour when storage is unavailable: the features disappear, and
  nothing else breaks.

## Out of scope

- Any server-side record of what a visitor has read.
- Syncing across devices, which would require an identifier.
- Marking entries read individually, or a read/unread inbox model.
- Restoring position automatically on load.

## Constraints

- No cookie is set and no request carries reading state. This is the point of
  the feature, not an implementation preference.
- Storage access throws rather than returning null in private browsing and when
  a visitor blocks site data. Every read and write is guarded, following
  `useAccessibilityPreferences`.
- Server-rendered markup cannot depend on stored state: the same HTML is served
  to everyone, and the marks are applied after hydration.
- Both themes, and both reduced-motion paths, per `docs/style/motion.md`.
- The progress indicator uses no scroll listener.

## Acceptance criteria

- [x] A first-ever visitor sees no "new" marks — everything being new is the
      same as nothing being new, and marking all of it is noise.
- [x] After a visit, returning with no newly published entries shows no marks.
- [x] An entry published after the stored timestamp is marked; one published
      before it is not.
- [x] Marks survive a reload within the same session rather than vanishing
      because the timestamp was just rewritten.
- [x] Clearing site data returns the site to its first-visit state.
- [x] With `localStorage` throwing, the journal and article pages render
      normally and no error reaches the console.
- [x] The resume invitation appears only when a stored position is far enough
      down the article to be worth restoring, and never scrolls on its own.
- [x] The progress indicator registers no scroll listener.
- [x] Every visible string resolves from `resources/js/copy/<locale>/`.
- [x] `npm run check`, `composer run lint:check` and `php artisan test` stay
      green.

## Tracking

- Linear: keep the primary issue key in `linear_issue:`
- GitHub Project: mirror `github_project_item`, `github_project_status`, and `release` in `docs/ai/github-project/roadmap-spec-issue-map.md`
- Obsidian: set `obsidian_note` to the repo mirror path under `docs/ai/obsidian/build-journal/`
- Codex execution: use the file-based workflow even if native `/speckit.*` commands are unavailable
