---
title: Bootstrap du repository pour un portfolio pilote par les specs
slug: repo-bootstrap-foundation
summary: Comment le repo a ete normalise depuis un simple README vers un workspace Laravel-first avec specs, docs et scaffolding AI operationnel.
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
role: Produit, architecture, implementation
stack:
    - Laravel 12
    - Inertia.js
    - Vue 3
    - TypeScript
    - Tailwind CSS
outcomes:
    - Etabli un socle stable local-first
    - Deplace la gouvernance et la constitution vers des emplacements explicites
    - Cree les premiers dossiers de specs et la forme initiale de roadmap
---

La premiere iteration de Sidewalk Studio partait d'un decalage : l'ambition du repository etait deja claire, mais le code versionne ne contenait encore qu'un `README.md` et une `LICENSE`.

Le but immediat n'etait pas de chasser le polish de surface. Il fallait d'abord construire un repo capable de supporter un vrai flux d'ingenierie :

- une application Laravel qui demarre localement sur Windows
- un systeme de specification visible
- une documentation qui explique les decisions au lieu de seulement lister des fichiers
- un espace pour le contexte AI operationnel futur et des skills reutilisables

Cela revenait a traiter le bootstrap lui-meme comme une feature.

## Ce qui a change

Le repository a ete normalise autour de trois couches.

### 1. Couche application

Un starter kit officiel Laravel 12 + Inertia + Vue est devenu la base. Cela a donne au projet une stack actuelle sans passer le premier jour a reconstituer toute la plomberie du framework.

### 2. Couche specification

La constitution a ete deplacee sous `.specify/memory`, tandis que le travail de feature a gagne un dossier `specs/` a la racine. Cette separation compte : la memoire outil reste cachee et operationnelle, alors que les specs de features approuvees restent lisibles et reviewables.

### 3. Couche documentation et AI

Les notes d'architecture ont bouge sous `docs/architecture`, et les conventions AI au niveau repo ont obtenu un emplacement dedie au lieu d'etre enterrees dans des snippets de prompts ad hoc.

## Pourquoi c'est important

Un repo portfolio doit prouver du jugement. Le premier signal ne tient pas au style visuel. Il tient a la capacite du repo a s'expliquer, a grandir proprement et a survivre a la deuxieme feature sans devenir une pile d'experiences deconnectees.
