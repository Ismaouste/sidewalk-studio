---
title: Orchestration du consentement avant les analytics
slug: consent-orchestration-before-analytics
summary: Pourquoi le systeme de consentement a ete construit comme couche de gating reutilisable avant toute connexion analytics, afin que la privacy reste une frontiere et non un detail eparpille.
status: published
published_at: 2026-03-08
updated_at: 2026-03-08
tags:
    - privacy
    - consent
    - architecture
seo_title: Orchestration du consentement avant les analytics
seo_description: Construire d'abord la couche de consentement garde les providers analytics optionnels et empeche la logique privacy de fuiter partout dans l'app.
client: Sidewalk Studio
role: Architecture produit et implementation frontend
stack:
    - CookieConsent
    - IframeManager
    - TypeScript
    - Inertia.js
outcomes:
    - Fixe le contrat public du consentement a trois categories explicites pour le v0
    - Ajoute les surfaces d'orchestration pour scripts et embeds sans figer un provider
    - Differe le choix analytics sans retarder le travail privacy-safe sur l'UI et le routage
---

Ce projet aura besoin d'analytics plus tard, mais il n'a pas besoin d'analytics en premier.

Cette distinction compte. Si Matomo ou PostHog entre dans le codebase avant qu'un contrat de consentement clair existe, le repo commence a enseigner la mauvaise lecon : la privacy devient un detail d'integration au lieu d'etre une frontiere systeme.

## Situation et contrainte

Le besoin n'etait pas seulement de produire un texte de conformite. Le site public avait deja besoin d'un comportement sensible au consentement autour des embeds, des analytics futurs et de textes administres.

La contrainte etait de resoudre cela sans :

- verrouiller le projet sur un vendor analytics
- disperser la logique privacy dans des composants sans lien
- pretendre qu'une integration analytics existait deja

Ce dernier point compte pour la credibilite. Beaucoup de code portfolio semble "complet" uniquement parce qu'il hardcode discretement des suppositions qu'il n'a pas encore gagne le droit de faire.

## La regle

Pour le v0, le modele de consentement n'expose que trois categories :

- `necessary`
- `analytics`
- `media`

Cela garde le contrat public explicite et compact. C'est suffisant pour couvrir les cookies, des placeholders analytics no-op et les embeds bases sur des iframes.

## Le choix d'implementation

Le frontend combine deux responsabilites :

- `CookieConsent` gere les preferences utilisateur, la persistence et l'UX de la modale.
- `IframeManager` empeche les embeds tiers de se charger tant que la categorie correspondante n'est pas acceptee.

Un registre interne s'intercale entre les deux pour que les providers futurs n'aient rien a connaitre de la couche UI.

## Pourquoi c'est mieux que de brancher un provider directement

Cela rend le churn provider peu couteux.

Une spec analytics ulterieure pourra ajouter des adaptateurs Matomo ou PostHog sans reconsiderer les categories de consentement, les actions de footer ou le modele de gating media. La logique de conformite reste lisible parce qu'elle est centralisee.

## Ce que cela a prouve

C'est un des premiers endroits ou Sidewalk Studio a cesse d'etre une simple coque design pour devenir un vrai systeme produit.

Cela a prouve que :

- la privacy pouvait etre modelee comme une infrastructure et non comme une copie ajoutee a la fin
- le frontend pouvait garder une UX publique calme tout en appliquant un vrai gating
- les integrations futures pouvaient rester optionnelles parce que la frontiere etait definie d'abord

Pour un recruteur ou un lecteur technique, c'est la vraie preuve ici : l'implementation ne se contente pas de mentionner la privacy, elle sequence l'architecture pour que le futur travail provider reste peu couteux et lisible.
