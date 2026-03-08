---
title: Les systèmes de contenu commencent par le routage et les métadonnées
slug: content-systems-routing-and-metadata
summary: Une note courte sur le fait qu'une publication en Markdown ne devient utile qu'une fois les slugs, les métadonnées et l'état de publication modélisés explicitement.
status: published
published_at: 2026-03-07
updated_at: 2026-03-07
tags:
    - content
    - seo
    - laravel
seo_title: Les systèmes de contenu commencent par le routage et les métadonnées
seo_description: Le Markdown n'est que la couche de stockage. Le vrai système commence avec le routage, l'état de publication et la discipline metadata.
---

Le Markdown est la partie facile.

La partie difficile consiste à décider ce qui rend un contenu publiable. Pour Sidewalk Studio, cette base repose sur un frontmatter explicite : titre, slug, résumé, état de publication, dates, tags et métadonnées SEO.

Cette décision permet à l'application de faire trois choses de manière sûre :

- rejeter tôt les documents incomplets
- exposer des URLs stables pour le sitemap et les canoniques
- séparer le contenu brouillon du contenu public sans infrastructure supplémentaire

Un fichier de contenu n'est pas seulement du texte. C'est un contrat entre l'intention éditoriale et le comportement applicatif.
