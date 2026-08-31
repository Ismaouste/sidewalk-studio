---
title: Orchestration du consentement avant les analytics
slug: consent-orchestration-before-analytics
translation_key: consent-orchestration-before-analytics
summary: Pourquoi le système de consentement a été construit comme couche de blocage réutilisable avant toute connexion à un vrai outil analytics.
status: published
published_at: 2026-03-08
updated_at: 2026-03-08
tags:
    - privacy
    - consent
    - architecture
    - work-sample
category: work
accent_tone: green
seo_title: Orchestration du consentement avant les analytics
seo_description: Construire d'abord la couche de consentement garde les outils analytics optionnels et évite de disperser la logique vie privée dans toute l'application.
client: Sidewalk Studio
role: Architecture produit et implémentation frontend
stack:
    - CookieConsent
    - IframeManager
    - TypeScript
    - Inertia.js
outcomes:
    - Fixé les catégories de consentement pour le v0
    - Ajouté les surfaces d'orchestration pour scripts et embeds
    - Différé les providers analytics sans bloquer le contrat UI
---

Ce projet aura besoin d'analytics plus tard, mais il n'a pas besoin d'un outil analytics en premier.

Cette distinction compte. Si Matomo ou PostHog entre dans le code avant qu'un contrat de consentement clair existe, le projet commence à enseigner la mauvaise leçon : la vie privée devient un détail d'intégration au lieu d'être une frontière système.

## La règle

Pour le v0, le modèle de consentement n'expose que trois catégories :

- `necessary`
- `analytics`
- `media`

Cela garde le contrat public explicite et compact. C'est suffisant pour couvrir les cookies, des placeholders analytics no-op et les embeds basés sur des iframes.

## Le choix d'implémentation

Le front combine deux responsabilités :

- `CookieConsent` gère les préférences utilisateur, la persistance et l'interface de la modale.
- `IframeManager` empêche les embeds tiers de se charger tant que la catégorie correspondante n'est pas acceptée.

Un registre interne s'intercale entre les deux pour que les outils futurs n'aient rien à connaître de la couche interface.

## Pourquoi c'est préférable à un branchement direct

Cela rend les changements d'outil peu coûteux.

Une future spécification analytics pourra ajouter des adaptateurs Matomo ou PostHog sans reconsidérer les catégories de consentement, les actions de footer ou le modèle de blocage des médias. La logique de conformité reste lisible parce qu'elle est centralisée.
