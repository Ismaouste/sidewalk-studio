---
title: Bootstrap du repository pour un portfolio piloté par les specs
slug: repo-bootstrap-foundation
summary: Comment le repo a été normalisé depuis un simple README vers un workspace Laravel-first avec specs, docs et scaffolding AI opérationnel.
status: published
published_at: 2026-03-07
updated_at: 2026-03-07
tags:
    - laravel
    - architecture
    - spec-driven
    - work-sample
category: work
accent_tone: dominant
seo_title: Bootstrap du repository pour un portfolio piloté par les specs
seo_description: 'Retour détaillé sur le premier jalon Sidewalk Studio : normalisation du repo, bootstrap Laravel et cadrage projet spec-first.'
client: Sidewalk Studio
role: Produit, architecture, implémentation
stack:
    - Laravel 12
    - Inertia.js
    - Vue 3
    - TypeScript
    - Tailwind CSS
outcomes:
    - Établi un socle stable local-first
    - Déplacé la gouvernance et la constitution vers des emplacements explicites
    - Créé les premiers dossiers de specs et la forme initiale de roadmap
---

La première itération de Sidewalk Studio partait d'un décalage : l'ambition du repository était déjà claire, mais le code versionné ne contenait encore qu'un `README.md` et une `LICENSE`.

Le but immédiat n'était pas de chasser le polish de surface. Il fallait d'abord construire un repo capable de supporter un vrai flux d'ingénierie :

- une application Laravel qui démarre localement sur Windows
- un système de spécification visible
- une documentation qui explique les décisions au lieu de seulement lister des fichiers
- un espace pour le contexte AI opérationnel futur et des skills réutilisables

Cela revenait à traiter le bootstrap lui-même comme une feature.

## Ce qui a changé

Le repository a été normalisé autour de trois couches.

### 1. Couche application

Un starter kit officiel Laravel 12 + Inertia + Vue est devenu la base. Cela a donné au projet une stack actuelle sans passer le premier jour à reconstituer toute la plomberie du framework.

### 2. Couche spécification

La constitution a été déplacée sous `.specify/memory`, tandis que le travail de feature a gagné un dossier `specs/` à la racine. Cette séparation compte : la mémoire outil reste cachée et opérationnelle, alors que les specs de features approuvées restent lisibles et reviewables.

### 3. Couche documentation et AI

Les notes d'architecture ont bougé sous `docs/architecture`, et les conventions AI au niveau repo ont obtenu un emplacement dédié au lieu d'être enterrées dans des snippets de prompts ad hoc.

## Pourquoi c'est important

Un repo portfolio doit prouver du jugement. Le premier signal ne tient pas au style visuel. Il tient à la capacité du repo à s'expliquer, à grandir proprement et à survivre à la deuxième feature sans devenir une pile d'expériences déconnectées.
