---
title: Les systemes de contenu commencent par le routage et les metadonnees
slug: content-systems-routing-and-metadata
summary: Une note courte sur le fait qu'une publication en Markdown ne devient utile qu'une fois les slugs, les metadonnees et l'etat de publication modeles explicitement.
status: published
published_at: 2026-03-07
updated_at: 2026-03-07
tags:
    - content
    - seo
    - laravel
seo_title: Les systemes de contenu commencent par le routage et les metadonnees
seo_description: Le Markdown n'est que la couche de stockage. Le vrai systeme commence avec le routage, l'etat de publication et la discipline metadata.
---

Le Markdown est la partie facile.

La partie difficile consiste a decider ce qui rend un contenu publiable. Pour Sidewalk Studio, cette base repose sur un frontmatter explicite : titre, slug, resume, etat de publication, dates, tags et metadonnees SEO.

Cette decision permet a l'application de faire trois choses de maniere sure :

- rejeter tot les documents incomplets
- exposer des URLs stables pour le sitemap et les canoniques
- separer le contenu brouillon du contenu public sans infrastructure supplementaire

Un fichier de contenu n'est pas seulement du texte. C'est un contrat entre l'intention editoriale et le comportement applicatif.
