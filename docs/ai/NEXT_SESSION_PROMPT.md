# Resume prompt

Paste the block below to start the next session. It is written to be handed to
an agent cold, so it repeats things you already know.

---

Reprends le travail sur sidewalk-studio (`C:\Users\ismae\sidewalk-studio`).

## Où on en est

PR #19 est **mergée** dans `main` (merge commit `09eb934`, 18 commits) et la
production Vercel est déployée. La CI est verte sur la matrice PHP 8.4 / 8.5 —
elle était rouge sur `main` depuis trois runs avant cette PR.

Ce qui a été livré : montée de stack (Laravel 13, Inertia 3, Vite 8 Rolldown,
ESLint 10, PHPUnit 13), Tailwind supprimé, copie bilingue dans
`resources/js/copy/`, NavTabs + AccessibilityPanel sur l'API Popover,
BreadcrumbTrail sur une `view-timeline`, transitions de page rendues à Inertia,
et deux features de mémoire locale (`specs/015-local-memory`).

Une branche est en cours : **`feat/light-blue-and-lit-grid`**, non poussée,
un commit. Elle contient la refonte de palette décrite plus bas. Commence par
la relire et décider si elle part en PR telle quelle.

## Baseline à ne pas casser

`npm run check`, `composer run lint:check`, `php artisan test`
(**85 tests / 756 assertions**), `npm run build:ssr`. Les deux thèmes se
testent au navigateur sur tout changement visuel.

## Toolchain (obligatoire)

PHP et Composer ne sont pas dans le PATH du Bash tool :

```
export PATH="/c/Users/ismae/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe:$PATH"
php /c/Users/ismae/.local/bin/composer.phar <cmd>
```

`npm run dev` a besoin de PHP dans le PATH aussi (plugin Wayfinder).
Si `composer install` renvoie HTTP 400 : rate limiting GitHub, exporte
`COMPOSER_AUTH` depuis `gh auth token`. Jamais `--prefer-source` :
`vlucas/phpdotenv` livre `tests/fixtures/env/nul.env`, et `nul` est un nom
réservé Windows qui avorte le checkout.

**N'utilise jamais `perl -i` sans suffixe de sauvegarde sur Windows** — ça
détruit le fichier et laisse un temporaire à nom aléatoire à côté. Ça a coûté
`tokens.css` cette session. Passe par l'outil Edit.

## Chantier 1 — finir la refonte visuelle

La branche en cours répond à trois retours :

- le thème clair était trop orange et ses gris trop tristes → `--sw-accent-sky`
  (bleu de calepinage) devient un vrai token, les neutres prennent une teinte
  bleutée, et une seconde lampe froide est posée sur la couche ambiante ;
- le violet du thème sombre était appliqué en couche épaisse sur _toutes_ les
  surfaces → les surfaces redeviennent du verre quasi incolore et le violet
  repasse dans le wash, avec le cyan comme deuxième couleur ;
- les lignes de fond étaient un peigne vertical à ~0,04 d'alpha effectif → une
  vraie grille de calepinage, majeures toutes les cinq divisions, révélée par
  la lumière (`mask-composite: intersect`).

Le fil d'Ariane est corrigé dans le même commit : 24px sous la barre au lieu de
40-64, une seule ligne, et troncature en `…` du dernier segment quand ça
déborde (vérifié à 900px).

**Ce qui reste à faire dessus :** faire relire par le sous-agent
`design-conformance-reviewer`, vérifier les contrastes sur les _nouvelles_
valeurs claires (les neutres ont bougé), tester mobile, et synchroniser
`docs/style/tokens.md` + `docs/style/theme-system.md` — c'est un point de
synchronisation obligatoire de CLAUDE.md.

## Chantier 2 — back-office éditorial complet et site agnostique

Une session dédiée, cadrée dans
**`docs/architecture/configurability-inventory.md`**. Lis-le avant toute chose :
il liste ce qui est déjà donnée, ce qui reste compilé en dur, et l'ordre
suggéré.

Le nœud est la décision §4 : les repositories préfèrent délibérément le
Markdown aux lignes en base (il y a des tests qui l'épinglent), donc l'admin
peut déjà éditer des publications que le site public ignore. Tout l'éditorial
est bloqué derrière ce choix — Markdown devient-il le format de seed
uniquement, la base faisant autorité ensuite ? Décide ça avant de construire
la moindre UI.

Objectif de sortie : `migrate:fresh --seed` produit un site neutre et
fonctionnel, et le seed Sidewalk par-dessus reproduit exactement le site
actuel.

## Dettes connues, à traiter quand elles croisent le chemin

- `useAccessibilityPreferences` n'initialise pas `data-motion` depuis
  `prefers-reduced-motion`, donc l'interrupteur du site et la media query se
  contredisent pour qui n'a rien réglé. C'est le correctif racine de plusieurs
  incohérences reduced-motion.
- `--sw-button-primary-bg` sur `--sw-button-primary-text` mesure **2,95:1** en
  thème clair (quasi-blanc sur orange). Préexistant, et c'est le bouton le plus
  visible du site.
- Un lien en `prefetch="hover"` avec `cache-for` produit **deux** cycles de
  visite complets quand le pointeur s'y attarde, donc deux fondus. Arbitrage de
  politique de prefetch.
- `--sw-tab-line` et `--sw-header-bg` n'ont aucun consommateur ;
  `html[data-scroll-lock]` dans `reset.css` n'a aucun écrivain.
- `actions/checkout@v4` et `actions/setup-node@v4` sont forcés sur Node 24 —
  passer en `@v5`.
- Les SVG de ContentVisual sortent en `max-age=3600` : un changement de palette
  met jusqu'à une heure à atteindre les visiteurs de retour. **Pertinent
  immédiatement** vu le chantier 1.

## Conventions

- Anglais pour tout le code, les docs et les specs. Le français reste pour la
  conversation.
- Jamais de couleur, police, espacement ou durée en dur : tokens `--sw-*`.
- `sunset` ne porte ni vert ni ambre ; les surfaces floutées saturent au-dessus
  de 1.
- Pas de CSS anchor positioning : essayé et annulé en `db61a48`, Chromium
  plaçait mal et ne déclenchait jamais `position-try-fallbacks`.
- Une seule branche pour un même chantier, même si le diff grossit ; garde en
  revanche les commits atomiques.
- Sous-agents à passer : `design-conformance-reviewer` après tout composant Vue
  ou CSS, `i18n-parity-reviewer` après `resources/content/pages/`.
