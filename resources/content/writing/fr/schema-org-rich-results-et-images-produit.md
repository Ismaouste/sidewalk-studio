---
title: Schema.org, rich results et images produit
slug: schema-org-rich-results-et-images-produit
summary: "Une note sur les données structurées e-commerce, les fiches produit, les pages collection, les infos d'organisation, et un détail agaçant : Google n'aime pas toujours son propre WebP."
status: published
published_at: 2026-03-08
updated_at: 2026-03-08
tags:
    - schema-org
    - seo
    - structured-data
    - notes-dev
seo_title: Schema.org, rich results et images produit
seo_description: Note technique sur les données structurées e-commerce, les fiches produit, les pages collection, les entités locales et les contraintes réelles des rich results Google.
category: note
publication_type: note
accent_tone: violet
---

Dans l'e-commerce, les données structurées ne se limitent jamais à coller un `Product` sur une fiche et passer à autre chose.

Dans les contextes HBJOAT, le vrai travail consiste souvent à faire tenir ensemble plusieurs niveaux :

- la fiche produit avec prix, disponibilité, image et offre ;
- les pages listing ou collection ;
- les pages marque, organisation ou point de vente ;
- les blocs plus transversaux comme la politique de retour, les horaires, les entités locales ou les informations de remboursement quand elles existent.

Sur le papier, `schema.org` donne l'impression que tout cela peut s'empiler proprement. En pratique, il faut surtout décider ce qui reste maintenable, ce qui colle au HTML réel, et ce qui a une chance de produire un enrichissement utile dans Google.

Je garde aussi un souvenir très concret d'un sujet plus industriel chez Prudhomme Transmissions : la question des fichiers 3D. Le format semblait intéressant, mais le chemin le plus propre restait finalement un produit téléchargeable gratuit plutôt qu'une promesse trop ambitieuse mal servie par le markup.

Le fun fact qui reste en tête concerne les images produit. Quand un résultat enrichi sort sur mobile avec le prix, le bouton vers le stock et l'image, on pourrait croire que Google favorise naturellement son propre format WebP. Dans les faits, JPEG ou PNG restent souvent les chemins les plus fiables pour obtenir un rendu propre et stable.

Cette note me rappelle une règle simple : les données structurées valent surtout quand elles restent lisibles, fiables et compatibles avec la réalité des assets et du catalogue.
