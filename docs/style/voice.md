# Voice

How the public copy is written, and why. Companion to `tokens.md`,
`components.md`, `motion.md` and `theme-system.md`: those govern how the site
looks, this governs what it says.

This file is in English because the repo is, but most of its examples are the
French copy, because that is where the failures were.

## Who is reading

Two people, and the site has to work for both without splitting into two sites:

- **A recruiter or hiring manager.** Wants scope, ownership, and decisions.
  Reads `/experience` and the case studies. Can take a technical noun without
  a gloss.
- **A business owner with a web need** — a shop, a venue, an institution.
  Reads `/`, `/services`, `/contact`. Knows what a fast site and a working
  product feed are worth, and does not know what a Core Web Vitals budget or
  a Meta DPA is.

Match the vocabulary to whoever reads _that page_. The failure this rule
exists to prevent: `/services` addressed itself to "merchants, venues, and
institutions" and then priced a site "designed against a Core Web Vitals
budget."

## Positioning: build and run, never inherit

**Do not describe the work as taking over what someone else built.** This was
the site's biggest voice problem and it ran through every page: `thesis` said
"Je reprends des plateformes en production", a strength said "Reprendre un
existant sans casser la production", a focus area was titled "Laravel et
reprise d'existant", and the contact page offered "Reprises, refontes".

It is bad for both readers. A recruiter reads someone who is handed other
people's debt. A business owner reads a repairman rather than the person who
would build the thing.

The capability is real and worth claiming — walking into a live platform and
shipping without stopping the business is genuinely hard. Claim it as a
capability, not as an identity:

| Instead of                                      | Write                                                                            |
| ----------------------------------------------- | -------------------------------------------------------------------------------- |
| Je reprends des plateformes en production       | Je construis des plateformes qui vendent, je les tiens en production             |
| Reprendre un existant sans casser la production | Je fais évoluer des plateformes qui vendent déjà, sans jamais couper la vente    |
| Laravel et reprise d'existant                   | Laravel en production                                                            |
| Reprises, refontes et mises en ligne            | Construction, refonte et mise en ligne — y compris quand la boutique tourne déjà |
| remise au propre de connecteurs                 | connecteurs conçus pour l'usage réel des équipes                                 |

The verbs that carry the positioning: **construire, faire tourner, tenir en
production, maintenir, faire évoluer**. Not _reprendre_, not _récupérer_, not
_remettre au propre_.

Same rule in English: _build, run, keep running, maintain, evolve_. Not _take
over_, not _legacy takeover_, not _cleanup_.

## What the work is called

**Not "e-commerce developer" alone.** The work is business platforms as much
as shops — a multi-tenant Laravel platform, an ERP/PIM connector layer, a
self-hosted reporting tool for a nonprofit. Pair them: *plateformes métier et
e-commerce*, *business platforms and online shops*. Narrowing to commerce
throws away half of what there is to show, and it is the half a CTO-track
recruiter is reading for.

**Do not say "à temps partiel" / "part-time" as a label.** It undersells: a
recruiter reads limited commitment, a client reads a side gig. Where the
rhythm is a real commercial fact — the daily-rate offer — state the fact
instead: *un à trois jours par semaine*. That says the same thing more
precisely and sounds like a decision rather than a limitation.

## Demonstrate, do not list

A summary built from classified keywords tells the reader which boxes the work
falls into. A summary built from sentences shows them what it looks like.

> ❌ "Sites web, e-commerce, ingénierie growth et direction technique à temps
> partiel."

> ✅ "Je construis des plateformes métier et des boutiques en ligne, puis je les
> fais tourner : la donnée produit qui circule entre les outils, les mises en
> ligne qui ne coupent pas la vente, les chiffres qui disent ce qui marche
> vraiment."

The second one is longer and reads faster, because each clause is a picture
rather than a category. When a hero or an intro starts feeling like a taxonomy,
replace the nouns with things that happen.

## Say what happened, in the first person

Most weak sentences on this site were weak the same way: an abstract noun did
the work a verb should do, and the actor disappeared.

> ❌ "Le travail a porté sur l'automatisation du déploiement, la lecture des
> retours d'exécution et les garde-fous quand la production ment."

> ✅ "J'ai automatisé les déploiements, puis j'ai fait en sorte que le pipeline
> rende compte de ce qui s'est réellement passé, et non de ce qu'on lui avait
> demandé de faire."

> ❌ "Travail centré sur la prévention, la confiance et la clarté d'usage."

> ✅ "Les personnes qui s'en servaient travaillaient dehors, souvent lors de
> rencontres uniques, et la donnée touchait à la santé. Ça excluait tout ce qui
> demandait une connexion stable, un mot de passe qu'on oublie, ou un serveur
> chez un tiers."

If a sentence could appear on any developer's site, it is not saying anything.
The test: does it contain a system, a number, a place, a constraint, or a
decision that could be wrong?

## Never promise content that does not exist

The home page card headed **"Des preuves, en production"** promised "une
plateforme de dispatch menée en CTO de fait, un CMS e-commerce sur mesure pour
le vintage de luxe, et ce site open source lui-même." None of the three
existed. The words _dispatch_ and _vintage_ appeared nowhere else in the
content tree, and a visitor clicking through found two unrelated case studies.

The most concrete, most clickable sentence on the site was a dead end, under a
heading that said "proof."

Before writing a claim that names a project, grep for it:

```
grep -ril "<the project>" resources/content/
```

If the only hit is the sentence you are writing, either write the case study
first or describe what actually exists.

Same rule for counts: `/experience` promised "Quatre contextes principaux" over
a list of three, one of which was a link to another page.

## Write French as French

The commercial pages were written in English and translated; the case studies
were written in French and translated. Both directions left seams. Actual
defects found and fixed:

- **"relisible"** — not a French word. A coinage from "reviewable" via _relire_.
- **"étapes expédiables"** — _expédier_ is to post something. It rendered
  "shippable stages" as "stages that can be sent by courier."
- **"dans la même personne"** (twice) — calque of "in the same person." French
  needs _chez_ or _par_.
- **"des bases qui restent lisibles en livrant"** — a dangling gerund; the
  _bases_ were doing the delivering.
- **"garder le regard branché sur"** — a strained mix of _garder un œil sur_
  and _rester branché_.
- **"mobile d'abord"** — French tech writing keeps _mobile-first_.
- **"sober"** in English, from _sobriété technique_. Not a word for software.
- **"outreach work outside institutional settings"** — _hors les murs_, French
  public-health vocabulary, rendered unreadable.

Rule: write the sentence in the target language from the fact, not from the
other language's sentence. If a French phrase has no natural English
equivalent (_hors les murs_, _musiques actuelles_), say what it means rather
than transliterating it.

## Do not explain the page to the reader

Editorial-model notes kept escaping into the rendered copy:

> ❌ "La page sert surtout à situer le terrain, pas à répéter la page d'accueil."
> ❌ "L'étude de cas doit montrer qu'un connecteur n'est pas juste un tuyau."
> ❌ "Une liste directe des publications, pour prolonger la visite sans détour."

The second one is the clearest case: a published case study telling the reader,
in the future tense, what it intends to demonstrate. That is a brief to
oneself. No visitor has ever wanted to know why a page exists.

Delete the frame and keep the content.

## Metaphor budget

The street-and-signage family (_Sidewalk_, _Repères_, _Signal_, _terrain_) is
the site's identity and it earns its place. It stops earning it when a
metaphor restates itself or replaces a fact:

> ❌ "l'idée, c'est que la porte est ouverte, et que c'est écrit sur la porte"
> ✅ "C'est écrit ici pour que personne n'ait à le demander."

Anthropomorphism is allowed **once**. The pipeline that "lies" is vivid. By the
time production _ment_, the system _fatigue_, and the repo _teaches_, it is a
tic. One live metaphor per page.

## The register that works

This already existed on the services page and is the benchmark for everything
else:

> "Trente minutes sur le contexte, la contrainte, et la question de savoir si
> je suis la bonne personne. Si ce n'est pas le cas, je le dis et j'oriente
> vers quelque chose d'utile."

Plain, specific, generous, and it tells you exactly what will happen.

## Mechanics

- **A colon inside a frontmatter scalar must be quoted.** `summary: A tool: it
does X` parses as a mapping and the declared-schema check refuses the page
  with "should be a text, got a mapping". Wrap the whole value in double
  quotes.
- **Never bulk-rewrite frontmatter with a regex that can match `- key: value`.**
  A sequence item whose value is a nested mapping looks like a scalar to a
  naive pattern, and quoting it destroys the structure. It broke 92 lines
  across the tree in one pass here.
- FR and EN must keep the same keys, the same array lengths, and the same
  nested shape. Adding a list item means adding it in both. Run the
  `i18n-parity-reviewer` subagent, or `php artisan test --filter=DeclaredPageContent`.
- Several tests pin page copy verbatim — the `thesis` line in
  `PublicPagesTest`, hero titles in `PublicLocaleResolutionTest`. Changing
  those strings means repointing the assertion in the same commit.
