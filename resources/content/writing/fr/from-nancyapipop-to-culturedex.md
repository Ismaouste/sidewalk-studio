---
title: De Nancy API POP à Culturedex
slug: from-nancyapipop-to-culturedex
translation_key: from-nancyapipop-to-culturedex
summary: Deux projets autour de l'API POP du ministère de la Culture, d'un prototype centré sur Nancy à un backend plus structuré pour explorer les notices.
status: published
published_at: 2026-03-08
updated_at: 2026-03-19
tags:
    - pop-api
    - nestjs
    - donnees-publiques
    - culture
seo_title: De Nancy API POP à Culturedex
seo_description: Deux projets autour de l'API POP, d'un prototype Nancy à un backend plus structuré pour rendre les notices culturelles plus lisibles.
category: journal
publication_type: journal
accent_tone: violet
schema: article
canonical: "{{site_url}}/fr/journal/from-nancyapipop-to-culturedex"
ogImage: /images/og/from-nancyapipop-to-culturedex.jpg
---

Le premier résultat utile n'a pas été une interface élégante. C'était un filtrage de l'API POP qui répondait enfin à une question simple : qu'est-ce que je peux récupérer autour de Nancy sans me perdre dans le vocabulaire de la source ?

## Problème

Les jeux de données publics ratent souvent le dernier mètre. La donnée existe, l'API existe, la documentation existe, et pourtant le passage de la source à un usage réel reste pénible.

C'était exactement le cas ici avec l'API POP du ministère de la Culture. La matière était riche, mais le vrai travail se situait ailleurs :

- comprendre quels filtres étaient réellement utiles sur un territoire précis ;
- apprendre le vocabulaire de la source et ses limites ;
- voir ce qui pouvait devenir une expérience de consultation lisible plutôt qu'un simple dump de réponse.

`nancyapipop` a constitué une première réponse. Le projet est resté proche du prototype et s'est concentré sur un objectif modeste mais indispensable : maîtriser la logique de filtrage liée à Nancy. Ce travail comptait parce qu'il réduisait l'écart entre "l'API existe" et "quelqu'un peut vraiment se servir du résultat".

## Décision

La décision n'a pas été de partir directement sur une plateforme plus large. Elle a été de découper le travail en deux temps.

D'abord, `nancyapipop` est resté proche du problème. Le but était d'apprendre la surface d'entrée : filtres, champs, limites et forme des requêtes utiles.

Ensuite, `culturedex` a repris la même intention avec plus de structure. Le projet a basculé vers un backend NestJS, une organisation TypeScript plus propre, et une ambition plus explicite autour de la consultation des notices. Cette deuxième étape n'avait de sens que parce que le premier projet avait déjà réduit l'incertitude sur la source elle-même.

J'aime bien cette séquence parce qu'elle évite une erreur fréquente sur les données publiques : construire l'architecture avant d'avoir compris le vocabulaire. L'alternative aurait été de commencer par une grosse coque technique, puis de découvrir plus tard que la vraie difficulté se trouvait dans les filtres, le mapping ou la manière dont la source décrivait les objets culturels. On obtient alors souvent un code plus propre et un produit moins juste.

## Résultat

Le résultat prend la forme de deux objets liés, avec des rôles différents.

`nancyapipop` a montré qu'un prototype territorial pouvait déjà rendre l'API POP plus lisible. `culturedex` a poussé la même direction plus loin, avec une structure backend plus facile à étendre, à documenter et à tester. Les deux projets racontent une manière de travailler que je retrouve ailleurs : commencer par apprendre la forme réelle de la source, puis décider quelle architecture le problème mérite vraiment.

Ces projets ne parlent pas directement d'ecommerce. Ils disent pourtant quelque chose d'utile sur ma manière d'aborder une surface de données. Que l'entrée soit une API culturelle publique ou un flux produit, la première tâche reste souvent la même : réduire l'ambiguïté, comprendre le vocabulaire, puis fabriquer une couche qui rende la source exploitable par autre chose que la personne qui a lu le payload brut.

C'est aussi pour cela que cet article rejoint [Les systèmes de contenu commencent par le routage et les métadonnées](/fr/journal/content-systems-routing-and-metadata). Dans les deux cas, le travail utile commence quand une source brute devient assez lisible pour porter des décisions stables. Et parce que l'ancrage territorial compte ici, il prolonge aussi la page [Localisation](/fr/local), même si le chemin reste avant tout technique.
