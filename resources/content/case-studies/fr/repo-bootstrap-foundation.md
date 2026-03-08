---
title: Bootstrap du repository pour un portfolio pilote par les specs
slug: repo-bootstrap-foundation
summary: Comment le repository est passe d'une coquille publique minimale a un workspace Laravel-first avec specs, docs operationnelles et une structure capable de supporter les vraies features suivantes.
status: published
published_at: 2026-03-07
updated_at: 2026-03-07
tags:
    - laravel
    - architecture
    - spec-driven
seo_title: Bootstrap du repository pour un portfolio pilote par les specs
seo_description: 'Retour detaille sur le premier jalon Sidewalk Studio : normalisation du repo, bootstrap Laravel et cadrage projet spec-first.'
client: Sidewalk Studio
role: Cadrage produit, architecture et implementation
stack:
    - Laravel 12
    - Inertia.js
    - Vue 3
    - TypeScript
    - Tailwind CSS
outcomes:
    - Remplace une coquille de repo par une application Laravel et Inertia exploitable
    - Deplace gouvernance, specs et contexte AI operationnel vers des emplacements explicites et reviewables
    - Cree une base capable de recevoir plus tard le locale, l'admin, le SEO et le consentement sans reset structurel
---

La premiere iteration de Sidewalk Studio partait d'un decalage : l'ambition du repository etait deja claire, mais le code versionne ne contenait encore qu'un `README.md` et une `LICENSE`.

Sur le papier, c'est un petit point de depart. En pratique, cela cree un vrai risque de livraison : tant qu'il n'existe ni frontiere applicative claire ni structure de projet lisible, chaque feature suivante doit resoudre le bootstrap en meme temps que son propre probleme.

Le but immediat n'etait pas de chasser le polish de surface. Il fallait d'abord construire un repo capable de supporter un vrai flux d'ingenierie :

- une application Laravel qui demarre localement sur Windows
- un systeme de specification visible
- une documentation qui explique les decisions au lieu de seulement lister des fichiers
- un espace pour le contexte AI operationnel futur et des skills reutilisables

Cela revenait a traiter le bootstrap lui-meme comme une feature avec ses propres contraintes de livraison.

## Situation et contraintes

Le repository devait devenir utile vite sans tomber dans le theatre de framework.

Les contraintes principales etaient simples :

- garder le local-first comme mode par defaut
- conserver une stack lisible pour la review et pour les entretiens
- traiter la doc et les specs comme des sorties produit reelles
- preparer une surface de preuve publique, pas seulement un bac a sable local

## Ce qui a change

Le repository a ete normalise autour de trois couches.

### 1. Frontiere application

Un starter kit officiel Laravel 12 + Inertia + Vue est devenu la base. Cela a donne au projet une stack actuelle sans passer le premier jour a reconstituer toute la plomberie du framework.

### 2. Frontiere specification

La constitution a ete deplacee sous `.specify/memory`, tandis que le travail de feature a gagne un dossier `specs/` a la racine. Cette separation compte : la memoire outil reste cachee et operationnelle, alors que les specs de features approuvees restent lisibles et reviewables.

### 3. Documentation et contexte operationnel

Les notes d'architecture ont bouge sous `docs/architecture`, et les conventions AI au niveau repo ont obtenu un emplacement dedie au lieu d'etre enterrees dans des snippets de prompts ad hoc.

## Pourquoi c'est important techniquement

Un repo portfolio doit prouver du jugement. Le premier signal ne tient pas au style visuel. Il tient a la capacite du repo a s'expliquer, a grandir proprement et a survivre a la deuxieme feature sans devenir une pile d'experiences deconnectees.

Ce bootstrap a prouve trois choses tres tot :

- le repo pouvait porter implementation, specs et docs sans en rendre une fausse
- les features suivantes pouvaient s'appuyer sur des frontieres explicites plutot que sur des suppositions cachees
- le site public pouvait devenir une surface d'ingenierie credible parce que le repository lui-meme etait deja lisible
