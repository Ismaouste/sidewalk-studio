---
name: i18n-content-parity
description: Runs the declared-schema parity tests over resources/content and explains what drifted. Use after editing any markdown file in that tree, or when the user asks for an i18n parity check.
---

# i18n Content Parity

Use this skill when:

- The user edits or asks to verify content under `resources/content/`.
- Before pushing a content change.
- The user invokes `/i18n-content-parity` or asks "is FR/EN aligned?".

## What changed, and why this skill is now short

This skill used to describe, in prose, how to compare two frontmatter blocks
by hand: set-equality on top-level keys, array lengths, nested shapes, empty
values. That checklist was real work and it caught real drift. It also missed
a defect that reached production, because a person comparing two Markdown
files does not evaluate YAML scalar resolution rules — an unquoted
colon-space in `fr/experience.md` resolved to a mapping instead of a string,
and the page rendered a JSON blob at its readers.

Every item on that checklist is now a test, driven by the declarations in
`app/Content/Schema/`. Running them is both faster and stricter than
performing the checklist, so **run the tests; do not re-implement them.**

## Process

1. Run the checks:

    ```
    php artisan test --filter="DeclaredPageContentTest|ContentRepositoryTest|LanguageFileParityTest"
    ```

    (PHP is not on `PATH` in the Bash tool — see `CLAUDE.md` for the prefix.)

2. If everything passes, say so in one line and stop. There is nothing a
   manual pass would add.

3. If something fails, the failure names the file and the path that drifted.
   Read the two files at that path and explain the drift in the report below.
   Diagnosis is the judgement call; detection is not.

## What each test covers

| Test                                                                 | Catches                                                  |
| -------------------------------------------------------------------- | -------------------------------------------------------- |
| `every page file validates against its declaration`                  | wrong types, missing keys, undeclared keys               |
| `the declarations and the content directory describe the same pages` | a page file added with no declaration                    |
| `both locales resolve to the same shape for every page key`          | list-length and nested-shape drift between FR and EN     |
| `no field is filled in one locale and blank in the other`            | an untranslated value that shape equality cannot see     |
| `every publication on disk validates against its declaration`        | the same, for the journal and case studies               |
| `translation keys pair each publication with its other locale`       | a publication whose translation is missing or duplicated |
| `every language file has the same key tree in both locales`          | drift in the server-resolved copy under `lang/`          |

## Output

```
i18n parity: PASS — 16 page files, 30 publications, 4 language files.
```

or

```
i18n parity: DRIFT

resources/content/pages/fr/experience.md
  professional_sections.0.paragraphs.3 should be a text, got a mapping
  → the scalar contains a colon-space, so YAML read it as a one-key
    mapping. Quote it. Note the French typographic space does not help:
    `marchand : création` still contains a colon-space.
```

## What is still a judgement call

- **Prose quality.** Nothing here reads the translation. A French sentence
  that is grammatical, present, and wrong is invisible to every test above.
- **Whether a drift is a bug or an intention.** A French section with two
  items where English has three may be a deliberate editorial choice. The
  test will fail; deciding what to do about it is the user's.

## What NOT to do

- Do not re-implement the comparison by hand. It is slower and weaker than
  the test, and the checklist form of it already missed a production defect.
- Do not auto-fix. Report, and let the user decide.
- Do not translate prose.
