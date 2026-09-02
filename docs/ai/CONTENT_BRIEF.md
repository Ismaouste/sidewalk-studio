# Content brief — what is still missing, and what only Isma can supply

Written 2026-09-02, after the voice pass. The editorial rules live in
`docs/style/voice.md`; this file is the backlog they could not close, because
closing it needs facts that are not in the repo.

Hand this file to a session that has Isma available to answer, or use it as a
checklist while writing the answers into the content files directly.

---

## 1. The home page can no longer promise three case studies

`focus_areas` on the home page used to say — under the heading **"Des preuves,
en production"** — that the site documented "une plateforme de dispatch menée
en CTO de fait, un CMS e-commerce sur mesure pour le vintage de luxe, et ce
site open source lui-même."

None of the three existed. _Dispatch_ and _vintage_ appeared nowhere else in
the content tree. A visitor clicking through found two case studies: a
deployment pipeline and this site's consent layer.

The card now describes the two that exist. **That is a truthful patch, not the
outcome we want.** The two promised projects are the strongest things on the
site and neither is written.

### What exists on disk

| Case study                                         | Status    | Public? |
| -------------------------------------------------- | --------- | ------- |
| `pipeline-deploiement-ecommerce`                   | published | yes     |
| `consent-orchestration-before-analytics`           | published | yes     |
| `product-data-flows-between-erp-pim-and-commerce`  | **draft** | 404     |
| `self-hosted-nonprofit-tooling-for-sensitive-data` | **draft** | 404     |

So the public "Études de cas" section holds two entries, one of which is about
this website.

### What is needed

**Atlas Dépannage** — the dispatch platform, referenced once on `/services`
("un produit construit sur périmètre cadré — la forme Atlas Dépannage") with no
explanation anywhere. That dangling reference has been removed; the offer now
describes the shape of the work instead. To write the case study:

- What the platform does, in one sentence a stranger understands.
- What "menée en CTO de fait" meant concretely: who else was on it, what you
  decided that nobody above you decided, what you were accountable for.
- The state machine — what states, and what goes wrong when they are wrong.
- The money flows — invoicing? payouts? commissions? This is the word that most
  needs precision and currently has none.
- One number that is safe to publish. No client names, no financials (that
  constraint is already recorded).

**Crown-DP** — the luxury-vintage e-commerce CMS. `crown-dp.com` 403s plain
fetches, so assets need a real browser. Needed: what the CMS does that an
off-the-shelf one does not, and why that mattered for vintage specifically
(one-of-a-kind stock? provenance? authentication?).

---

## 2. Two case studies are outlines wearing a case study's clothes

`product-data-flows-between-erp-pim-and-commerce` and
`self-hosted-nonprofit-tooling-for-sensitive-data` are `status: draft`, and
they are drafts in the strict sense: their bodies are section headings and
topic bullets. One of them literally carries a heading called **"Résultat
attendu" / "Expected outcome"** — a heading that tells the reader the case has
not happened yet.

The self-addressed brief that was published in the French version ("L'étude de
cas doit montrer que…") has been rewritten. The rest still needs writing.

**The product-data one is the easy win**, because the facts are already
published elsewhere on this site. `/experience` says:

> "Sur le PIM maison, j'ai construit les algos Python de scrap et
> d'enrichissement qui alimentent aujourd'hui +20 000 fiches produit, leurs
> images et leurs vidéos."

> "Côté commerce, j'ai conçu et tenu les connecteurs entre l'ERP, le PIM et les
> catalogues marchand : création produit automatique et orchestration de
> synchronisation vers Google Merchant Center, Facebook Catalog."

That is a case study's skeleton in two sentences. What it still lacks is the
part only Isma has: **what was broken before, and what changed after.**
+20 000 is a volume, not an outcome. What used to take how long? What used to
break? What stopped breaking?

---

## 3. The best case study deletes its own author

`pipeline-deploiement-ecommerce` is the strongest writing on the site —
concrete, checkable, `docker service update`, exit `0`, digest comparison, ECR
lifecycle, disk pressure. It is also written almost entirely without an "I".
"La décision n'a pas été de changer complètement d'outil." Whose decision?

A hiring manager reads four screens without learning whether Isma diagnosed
this, fixed it, or watched it happen. The facts are already right; the pass
needed is putting the actor back into the sentences.

The `/experience` Infrastructure section has already been rewritten this way
and can serve as the model.

---

## 4. Smaller open items

- **`local.md` diverges in meaning between locales**, not just wording. The FR
  `engagements[1].summary` carries a sentence with no English counterpart and
  vice versa. Shape parity passes because shape is all it checks. Decide which
  claim is the real one and say it in both.
- **Two case-study pairs disagree on metadata across locales**: `published_at`
  and `updated_at` differ by a day on `consent-orchestration-before-analytics`,
  and `publication_type` is `reference` in FR against `case_study` in EN on
  `pipeline-deploiement-ecommerce`. Inert at runtime —
  `normalizePublicationType()` forces `case_study` for the section — but the
  files disagree on disk.
- **The four poetic questions in the editorial back office still have no
  answers.** They are the owner's voice by design, so nothing was written into
  them. Until they are answered the chronology has no marginal notes.
- **The seeded experience positions have no dates**, only their seeded labels.
  Filling `started_on` and clearing `date_label` stops the page needing a
  January edit every year.
