---
title: Pourquoi le SSR reste pret mais differe
slug: why-ssr-is-prepared-but-deferred
summary: Le repo garde une voie propre vers le SSR sans payer le cout operationnel d'un runtime SSR avant que le contenu, le routage, les metadonnees et les surfaces de preuve soient stabilises.
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

## Pourquoi le differer etait la decision disciplinee

Le projet portait deja assez de sujets couples :

- Laravel, Inertia et Vue comme base runtime
- contenu structure et payloads SEO
- comportement public sensible au consentement
- repository public cense rester lisible

Ajouter un runtime SSR complet par-dessus aurait ete techniquement possible, mais ce n'etait pas le risque le plus utile a traiter d'abord.

Cela aurait aussi rendu le repo public moins honnete. Revendiquer une maturite SSR avant que le modele de contenu, le chemin metadata et les surfaces de preuve soient stabilises aurait surtout optimise l'optique, pas le sequencing.

## Ce que cela a prouve

Pret mais differe n'est pas un compromis vague ici. C'est une decision explicite de sequencing.

Cela a prouve que le repo peut :

- preserver des options d'architecture futures sans les payer trop tot
- garder le local-first simple tout en respectant les besoins SEO
- expliquer pourquoi une capacite est differee, ce qui vaut souvent plus qu'une revendication prematuree de maturite

Ce dernier point compte en pratique. L'ingenierie mature se voit souvent dans ce que tu choisis de ne pas livrer trop tot.
