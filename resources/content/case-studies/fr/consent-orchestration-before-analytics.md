---
title: Orchestration du consentement avant les analytics
slug: consent-orchestration-before-analytics
summary: Pourquoi le système de consentement a été construit comme couche de gating réutilisable avant toute connexion à un vrai provider analytics.
status: published
published_at: 2026-03-08
updated_at: 2026-03-08
tags:
    - privacy
    - consent
    - architecture
seo_title: Orchestration du consentement avant les analytics
seo_description: Construire d'abord la couche de consentement garde les providers analytics optionnels et empêche la logique privacy de fuiter partout dans l'app.
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

Ce projet aura besoin d'analytics plus tard, mais il n'a pas besoin d'analytics en premier.

Cette distinction compte. Si Matomo ou PostHog entre dans le codebase avant qu'un contrat de consentement clair existe, le repo commence à enseigner la mauvaise leçon : la privacy devient un détail d'intégration au lieu d'être une frontière système.

## La regle

Pour le v0, le modèle de consentement n'expose que trois catégories :

- `necessary`
- `analytics`
- `media`

Cela garde le contrat public explicite et compact. C'est suffisant pour couvrir les cookies, des placeholders analytics no-op et les embeds basés sur des iframes.

## Le choix d'implementation

Le frontend combine deux responsabilités :

- `CookieConsent` gère les préférences utilisateur, la persistence et l'UX de la modale.
- `IframeManager` empêche les embeds tiers de se charger tant que la catégorie correspondante n'est pas acceptée.

Un registre interne s'intercale entre les deux pour que les providers futurs n'aient rien à connaître de la couche UI.

## Pourquoi c'est mieux que de brancher un provider directement

Cela rend le churn provider peu coûteux.

Une spec analytics ultérieure pourra ajouter des adaptateurs Matomo ou PostHog sans reconsidérer les catégories de consentement, les actions de footer ou le modèle de gating media. La logique de conformité reste lisible parce qu'elle est centralisée.
