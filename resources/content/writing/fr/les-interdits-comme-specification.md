---
title: Les interdits comme spécification
slug: les-interdits-comme-specification
summary: Dans un design system joaillerie, les règles utiles ne disent pas seulement quoi faire ; elles disent aussi ce qu'il faut éviter pour garder l'identité visuelle technique.
status: published
published_at: 2026-03-18
updated_at: 2026-03-19
tags:
    - design-system
    - delivery
    - journal
seo_title: Les interdits comme spécification
seo_description: Dans un design system joaillerie, des interdits explicites aident les composants Vue et l'identité visuelle technique à survivre au temps et aux changements d'équipe.
category: journal
publication_type: journal
accent_tone: sun
schema: article
canonical: https://sidewalk-studio.vercel.app/fr/journal/les-interdits-comme-specification
ogImage: /images/og/les-interdits-comme-specification.jpg
---

Le moment décisif n'arrive pas quand un composant Vue est enfin documenté. Il arrive quand l'équipe sait aussi ce qu'elle ne doit pas faire, même quand personne n'est disponible pour refaire l'arbitrage d'un design system joaillerie.

## Problème

Quand on documente un design system, on décrit souvent les composants, les espacements, les couleurs et les variantes autorisées. C'est nécessaire. Mais ce n'est pas suffisant dans les environnements où les pages vivent longtemps, où l'équipe change, et où il n'y a pas toujours un designer permanent pour arbitrer.

Sans règles négatives, le système devient vite trop ouvert. Une homepage finit par accueillir un prix qui casse le ton de marque. Un bouton plein apparaît parce qu'il semblait plus visible. Un titre passe en gras alors que toute la logique typographique du site reposait sur autre chose. Personne n'a forcément mal travaillé. Il manque seulement une frontière formulée.

## Décision

La décision que je trouve la plus utile consiste à documenter les interdits avec le même sérieux que les règles positives.

Dire qu'un hero de homepage ne montre pas de prix, qu'un bouton plein n'appartient pas à un certain univers visuel, ou qu'un titre ne doit pas passer en gras n'a rien d'anecdotique. C'est une manière de rendre la décision durable. L'équipe n'a plus besoin de deviner l'intention initiale. Elle peut s'appuyer sur une contrainte explicite.

L'alternative paraît plus souple, mais elle coûte plus cher. Sans interdits, chaque nouvelle page rediscute en silence ce qui aurait dû rester stable. Développement, contenu et design finissent alors par rejouer les mêmes micro-arbitrages, avec des résultats un peu différents à chaque fois.

## Résultat

Des interdits bien formulés allègent le travail. Ils réduisent le nombre de décisions à reprendre et ils protègent le caractère du site quand la production continue sans la même équipe d'une semaine à l'autre.

Ils ont aussi un autre effet utile : ils obligent à mieux comprendre la marque ou le produit. Pour écrire "ne pas faire cela", il faut savoir ce que ce choix dégrade. La documentation devient alors plus précise. Elle cesse d'être une galerie de composants pour devenir une traduction technique d'intentions éditoriales, visuelles et commerciales.

Je vois donc les interdits comme une forme de spécification négative. Ils disent moins "voici le rendu attendu" que "voici ce que nous refusons pour garder la cohérence du système". Dans un projet vivant, c'est souvent ce qui protège le mieux la qualité sur la durée.

Cet article rejoint [Quand un déploiement réussi ne l'est pas](/fr/journal/quand-un-deploiement-reussi-ne-lest-pas) par un autre biais que le design. Dans les deux cas, le sujet central est le même : rendre explicite ce qui devait l'être au lieu de laisser une ambiguïté survivre dans le système.
