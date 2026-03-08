# Codex Handoff: Career and Editorial Continuation

Use this brief when returning to the repository for the next content-heavy phase.

## Current state

The public portfolio surface is now close to a first stable release:

- navigation is centered on `Hello`, `Expériences`, `Journal`, and `Contact`
- the visual language, loader, themes, mobile navigation, and GitHub Pages preview are in place
- French copy has been refined first, then English was updated to stay precise and useful rather than literal
- the professional narrative is now centered on full-stack e-commerce, product data, connectors, tracking, consent, CMS work, and public/non-profit tooling

## Career context to preserve

- The core long-term professional context is Jewely / Flippad.
- The portfolio should keep that as the main line of experience rather than overemphasizing secondary contexts.
- Aremedia is meaningful, but it is not the central framing of the career.
- Target opportunities are respectful teams in Nancy / Grand Est / the wider cross-border region, with strong interest in:
  - left-leaning or socially aware companies
  - co-ops
  - associations
  - public-service institutions
  - University of Lorraine opportunities
- The tone should stay senior, direct, warm, and specific. Avoid defensive phrasing, empty portfolio language, and generic Anglo product clichés.

## Editorial backlog to prioritize

The next phase is mainly about better public content, especially:

1. notes
2. journal entries
3. case studies

Priority themes already identified:

- structured data in e-commerce, especially jewelry/watch product data
- collection pages, product pages, organization/local-business data, returns, opening hours
- Google rich results image constraints and the practical JPEG/PNG vs WebP issue
- ERP / PIM / product-feed circulation
- auto-hosted nonprofit tools dealing with sensitive or health-related data
- lack of funding for nonprofit digital tooling and the operational consequences
- Linux ecosystems, self-hosting, and practical alternatives such as Framasoft services
- practical SEO topics: sitemaps, `robots.txt`, metadata, structured data, merchant/catalog feeds

## Writing rules

- Prefer short, concrete titles.
- Keep French primary when shaping new ideas, then translate precisely into English.
- Notes can stay compact and technical.
- Journal entries can be richer, more reflective, and more situated.
- Case studies should focus on constraints, decisions, outcomes, and why the work mattered.
- Do not revert to placeholder copy once a real profile-specific framing exists.

## UI / UX follow-up already identified

- footer accessibility control for motion reduction, theme-transition reduction, and alternate contrast/color modes
- locale routing migration to `/fr/...` and `/en/...`
- further refinement of publication listings and filters
- continued front-end performance audits once the content layer is more stable

## Validation

When runtime or content behavior changes:

```powershell
php artisan test
npm run types:check
npm run build
php artisan route:list
```

When GitHub Pages preview needs to be checked:

```powershell
php artisan site:export-static-preview --locale=fr --output=dist/static-preview --base=/sidewalk-studio/
```

## Suggested next tasks

- promote the best current drafts into published notes or case studies
- replace the remaining thin publication intros with sharper editorial framing
- migrate locale handling to real `/fr/...` and `/en/...` paths
- add accessibility toggles in the footer
