---
title: YTMusic Liked Sorter
slug: ytmusic-liked-sorter
summary: Un petit outil personnel devient utile dès qu'il rend un historique YouTube de nouveau triable, lisible et exploitable.
status: published
published_at: 2026-03-07
updated_at: 2026-03-19
tags:
    - ytmusic-liked-sorter
    - notes-dev
    - hobby
seo_title: YTMusic Liked Sorter
seo_description: YTMusic Liked Sorter remet un peu d'ordre dans un historique YouTube devenu trop dense pour être relu ou filtré correctement.
category: note
publication_type: note
accent_tone: coral
schema: article
canonical: https://sidewalk-studio.vercel.app/fr/journal/ytmusic-liked-sorter
ogImage: /images/og/ytmusic-liked-sorter.jpg
---

Le point de départ est très simple : trop de morceaux aimés, pas assez de moyens pour les relire utilement, et l'impression qu'un historique existe sans être vraiment réutilisable.

## Problème

Les plateformes d'écoute savent accumuler. Elles savent moins bien aider à retrouver. Quand une liste de likes devient longue, elle reste théoriquement accessible, mais elle cesse d'être vraiment exploitable. On se souvient qu'un morceau existe sans avoir un moyen simple de le retrouver dans le bruit.

J'aime bien ce type de gêne parce qu'elle ne demande aucun grand récit. Le problème est visible, le besoin est concret, et l'échelle reste humaine.

## Décision

La décision a été de garder l'outil étroit.

Il lui fallait seulement trois choses :

- ingérer un historique existant ;
- offrir un tri ou un filtrage plus utile que l'interface d'origine ;
- produire une sortie assez lisible pour être immédiatement réutilisable.

Le dépôt public est disponible sur [GitHub](https://github.com/Ismaouste/ytmusic-liked-sorter), précisément parce que l'intérêt du projet se situe là : une logique claire de récupération, de filtrage et de restitution. L'alternative aurait été de le gonfler artificiellement avec des fonctionnalités produit qui n'auraient rien apporté au besoin de départ.

## Résultat

Le résultat reste modeste, et c'est très bien ainsi. L'historique redevient triable, on retrouve plus facilement des choses perdues dans le volume, et l'outil garde un rapport direct à son problème d'origine.

Je trouve ce genre de projet utile parce qu'il montre une autre relation au code. Pas une promesse disproportionnée, juste un outil propre qui sert réellement. À petite échelle, il pose les mêmes questions que des systèmes plus larges : où se trouve la vraie friction, quelle donnée mérite d'être remise en forme, et quelle interface minimale suffit pour rendre le résultat exploitable.

Cette modestie fait partie de l'intérêt du projet. Si l'historique redevient lisible, l'outil a déjà gagné. Il n'a pas besoin d'un récit produit plus large pour justifier son existence.

Je crois que c'est aussi pour cela que ce type d'outil reste agréable à fabriquer. Il garde le code proche de son usage réel. On sait très vite si le résultat sert, parce que l'on retombe immédiatement sur le problème de départ.

Cette proximité entre besoin et solution évite aussi beaucoup de bruit technique inutile. Le projet reste compréhensible, révisable et facile à reprendre.

Cette note rejoint [Volontariat NJP et petits outils utiles](/fr/journal/volontariat-njp-et-petits-outils), parce qu'elle repose sur la même idée : garder la taille du projet proportionnée à l'inconfort qu'il résout.
