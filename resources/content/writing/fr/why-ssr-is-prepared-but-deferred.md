---
title: Pourquoi le SSR reste pret mais differe
slug: why-ssr-is-prepared-but-deferred
summary: Le repo garde une voie vers le SSR sans payer le cout operationnel d'un runtime SSR dans le premier jalon local-first.
status: published
published_at: 2026-03-08
updated_at: 2026-03-08
tags:
    - ssr
    - inertia
    - strategy
seo_title: Pourquoi le SSR reste pret mais differe
seo_description: Sidewalk Studio reste compatible avec le SSR plus tard, sans ajouter trop tot la complexite d'exploitation tant que les fondations de contenu et de SEO ne sont pas stabilisees.
---

Le SSR est utile, mais il n'est pas gratuit.

Pour ce v0, le projet conserve le point d'entree SSR et evite les choix d'architecture qui bloqueraient son activation plus tard. Ce n'est pas la meme chose que d'activer le SSR immediatement.

La raison est pragmatique : le premier jalon porte sur les frontieres systeme, le modele de contenu et une orchestration respectueuse de la vie privee. Ajouter un runtime SSR trop tot ferait grossir les pieces mobiles avant que le modele d'information et de SEO soit stabilise.

Le repo choisit donc une voie mediane :

- garder le fichier SSR et l'affordance de build en place
- fournir les metadonnees cote serveur pour la premiere requete
- differer le runtime SSR complet a une spec ulterieure une fois l'architecture de l'information stabilisee
