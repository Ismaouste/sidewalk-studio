---
linear_issue: TODO
github_project_item: TODO
github_project_status: implemented
obsidian_note: TODO
release: TODO
title: Editorial Back Office
status: implemented
---

# Feature Specification: Editorial Back Office

The back office becomes a place to run the site rather than a place to edit
its settings. Three things land together: the career chronology moves from
prose inside a page payload to rows with real dates, the site starts asking
its owner a short set of questions whose answers surface beside the work, and
`/admin` opens on what is unfinished instead of on a form.

## Problem

Three problems, and they share a shape: the content model was describing
things it could not compute with.

**The chronology could not be ordered.** Every position lived as an object
inside three arrays in the `experience` payload, and its period lived as prose
inside the same string as the role — `Développeur e-commerce — 2023-2026` was
one field. `Projects.vue` recovered the two halves in the browser with
`eyebrow.split(/\s+[—–-]\s+/)`, so a role containing a spaced dash would have
been read as a date. The order of the chronology was the order of the items in
the array. Nothing could ask when a job started, because nothing stored it,
and every January the page needed an edit to stay true.

**Half the profile had nowhere to live.** This site is the public artifact for
a profile that is neither a pure engineering portfolio nor a pure design one.
A list of positions says the first half. There was no surface for the second —
how the work is thought about — and `EditorialSpread` had in fact carried an
unused marginalia slot since it was written, an italic display quote over a
micro-typographic caption, which the declared schema would refuse to fill from
the page payload because `marginalia` is not declared there.

**The back office answered the wrong question.** `/admin` redirected to
Settings: the screen an operator needs least often, and one that answers no
question they arrived with. Nothing anywhere said what was unfinished.

## Desired outcome

- A position is a row, ordered by its own dates, editable without a developer.
- The site asks its owner a few short questions, and the answers appear beside
  the work rather than in a page nobody reaches.
- Opening `/admin` says what is open and what the public site does about it
  meanwhile.
- Nothing a reader could see changes on the day this lands.

## Recommendation

**Rows for the chronology, a declaration for the questions, derivation for the
dashboard.** Each half of that sentence is a deliberate refusal of the
obvious alternative.

The chronology is rows because dates are the thing it needed and prose could
not give. But `started_on` is only the _ordering_ truth: `date_label` overrides
the display, because two of the four seeded entries honestly say `Avant 2023`
and that is not a range with a missing half. Seeding splits the eyebrow into a
role and a label rather than into dates — guessing a date would put an
invented fact in a CV — so the page renders identically and filling in real
dates is what hands the range over afterwards.

The questions are declared in code and not stored, like a page's slots, because
a question an operator could invent is a question the layout has nowhere to
put. Their prompts live in the language files because the reader sees them: the
caption under an answer is the question that produced it, which is what turns a
pull quote into a Q&A.

The dashboard derives everything and stores nothing. A cached count is a count
that can be wrong, and a back office that lies about what is unfinished is
worse than one that says nothing.

## In scope

- `experience_entries`: one row per position per language, paired across the
  two by a `translation_key`, seeded from the existing payload byte for byte.
- `/admin/experience`: list in chronological order, create, edit, remove —
  every write touching both languages.
- `questionnaire_answers` and four declared poetic questions, surfacing as the
  marginalia of the experience spreads.
- `/admin/questionnaire`: the whole set, both languages side by side, saved in
  one request.
- `/admin`: a derived digest of what is unfinished, what the site does about it,
  the size of the record, and recent activity.

## Out of scope

- Reordering positions by hand. `position` exists as a tie-break inside one
  date and is not exposed; the dates are meant to do this work.
- A second surface for the questionnaire. `SURFACE_EXPERIENCE` is an
  enum-shaped string precisely so the next one is an entry rather than a
  second mechanism.
- Retiring the page payload for the three section families. Production ships no
  SQLite, so the payload remains the fallback and is not a legacy path.
- Publications keep the tree editor. Their declaration exists and validates,
  so the same `SchemaField` can drive them later.

## Constraints

- Both locales stay in shape parity. A chronology that could grow a
  French-only row would break, on the first save, an invariant the rest of the
  site holds everywhere.
- Every database read is guarded by `Schema::hasTable`.
- Unanswered and undated are resting states, not errors. Nothing renders a
  hole; a page with nothing to add renders exactly as it did.
- An empty answer deletes its row rather than storing a blank, so no read has
  to know the difference between an absent row and a present empty string.

## Acceptance criteria

- [x] The rows reproduce the `experience` payload exactly, compared as encoded
      JSON so key order cannot drift.
- [x] The public chronology follows `started_on`, with undated entries last.
- [x] Creating a position files it in both languages under one key; removing
      removes both.
- [x] A date label hides the dates without discarding them.
- [x] With nothing answered, the public page is unchanged.
- [x] An answer becomes a marginal note whose caption is the question, in the
      reader's language.
- [x] `/admin` renders the dashboard; the signed-out branches are unchanged.
- [x] The dashboard's unfilled-slot count reproduces the known figures —
      `experience` 8 per language, `contact` 1 per language.

## Tracking

- Linear: keep the primary issue key in `linear_issue:`
- GitHub Project: mirror `github_project_item`, `github_project_status`, and `release` in `docs/ai/github-project/roadmap-spec-issue-map.md`
- Obsidian: set `obsidian_note` to the repo mirror path under `docs/ai/obsidian/build-journal/`
