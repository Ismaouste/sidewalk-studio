---
title: Pourquoi le SSR reste prêt mais diffère
slug: why-ssr-is-prepared-but-deferred
summary: Le repo garde une voie vers le SSR sans payer le coût opérationnel d'un runtime SSR dans le premier jalon local-first.
status: published
published_at: 2026-03-08
updated_at: 2026-03-08
tags:
    - ssr
    - inertia
    - strategy
    - notes-dev
category: journal
accent_tone: violet
seo_title: Pourquoi le SSR reste prêt mais diffère
seo_description: Sidewalk Studio reste compatible avec le SSR plus tard, sans ajouter trop tôt la complexité d'exploitation tant que les fondations de contenu et de SEO ne sont pas stabilisées.
---

Le SSR est utile, mais il n'est pas gratuit.

Pour ce v0, le projet conserve le point d'entrée SSR et évite les choix d'architecture qui bloqueraient son activation plus tard. Ce n'est pas la même chose que d'activer le SSR immédiatement.

La raison est pragmatique : le premier jalon porte sur les frontières système, le modèle de contenu et une orchestration respectueuse de la vie privée. Ajouter un runtime SSR trop tôt ferait grossir les pièces mobiles avant que le modèle d'information et de SEO soit stabilisé.

Le repo choisit donc une voie médiane :

- garder le fichier SSR et l'affordance de build en place
- fournir les métadonnées côté serveur pour la première requête
- différer le runtime SSR complet à une spec ultérieure une fois l'architecture de l'information stabilisée
