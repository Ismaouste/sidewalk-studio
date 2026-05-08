---
name: i18n-content-parity
description: Diffs FR vs EN frontmatter under resources/content/pages to detect parity drift (missing keys, list-length mismatch, empty translations, nested-shape mismatch). Use after editing any markdown file in that tree, or when the user asks for an i18n parity check.
---

# i18n Content Parity

Use this skill when:

- The user edits or asks to verify content under `resources/content/pages/`.
- Before pushing a content change.
- The user invokes `/i18n-content-parity` or asks "is FR/EN aligned?".

## Scope

Files matching `resources/content/pages/{fr,en}/<slug>.md`. Each file is markdown with a YAML frontmatter block. Only the frontmatter is in scope — prose translation quality is **not** checked here.

## Process

1. List slugs in `resources/content/pages/fr/` and `resources/content/pages/en/`.
2. Compute three sets:
   - `aligned` — slugs present in both locales
   - `fr-only` — slugs present only in FR
   - `en-only` — slugs present only in EN
3. For each `aligned` slug, parse both frontmatter blocks and check:
   - **Top-level keys** — set equality.
   - **Array lengths** — for any key whose value is a list, the count must match (e.g., `professional_sections`, `detail_groups`, `stack_groups`, `contexts`, `positioning`).
   - **Nested object shape** — for arrays of objects, every item-pair must have the same key set.
   - **Empty values** — flag entries that are empty string, `null`, `TBD`, or `TODO` in only one locale.

## Output

Report per slug, grouped by status:

```
slug: experience
  ✗ Top-level key missing in en: side_projects_widget
  ✗ Array length mismatch at professional_sections: fr=3 en=2
  ⚠ Empty value at fr.associative_note_widget.title (en has content)

slug: home
  ✓ aligned

Single-locale slugs:
  fr-only: foo
  en-only: bar
```

End with a one-line summary: `N slugs aligned, M with drift, K single-locale`.

## Bonus mode

If the user passes `--suggest`, propose a draft stub for the missing key/locale, clearly marked as `// DRAFT — review before merging`. Never auto-apply.

## What NOT to do

- Do not translate prose. We only check structure.
- Do not auto-fix. The user must decide what to add or remove.
- Do not check files outside `resources/content/pages/`.
