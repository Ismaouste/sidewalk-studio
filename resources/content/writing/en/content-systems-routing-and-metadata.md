---
title: Content systems start with routing and metadata
slug: content-systems-routing-and-metadata
summary: Markdown becomes publishable once routing, metadata, and publication state are explicit enough to drive canonical URLs, archives, and sitemap output.
status: published
published_at: 2026-03-07
updated_at: 2026-03-19
tags:
    - content
    - seo
    - laravel
    - metadata
seo_title: Content systems start with routing and metadata
seo_description: Routing and metadata are what turn Markdown into a publishable content system with stable URLs, canonicals, and archive rules.
category: journal
publication_type: journal
accent_tone: violet
schema: article
canonical: "{{site_url}}/en/journal/content-systems-routing-and-metadata"
ogImage: /images/og/content-systems-routing-and-metadata.jpg
---

The problem showed up before the first article was worth reading. A Markdown file existed, but the repo still had to decide whether it belonged on `/en/journal/...`, whether it belonged in the sitemap, and whether a draft should stay invisible.

## Problem

Markdown is easy to author. It is not enough to run a public content system.

The moment a site needs a canonical URL, an archive, a sitemap, a French fallback, and a draft state, the file alone stops being the system. The system is the set of rules that decides whether a document is complete, whether it is public, and where it lives.

That was the real problem in this repo. Without explicit metadata, every next step became fragile:

- a missing slug could break a public route;
- a missing description could weaken the page-level SEO payload;
- a draft could leak into an index if the application only saw "a file exists";
- a localized entry could quietly create duplicate URLs if the routing rules stayed vague.

In practice, content quality and SEO quality were already tied together. The repo needed to treat content as data with a stable contract, not as loose prose sitting in a folder.

## Decision

The decision was to validate the publishing contract at the repository layer instead of leaving it implicit in templates or controllers.

The `ContentRepository` now refuses incomplete frontmatter before the page can render:

```php
foreach (['title', 'slug', 'summary', 'status', 'published_at', 'updated_at', 'tags', 'seo_title', 'seo_description'] as $field) {
    if (! array_key_exists($field, $matter)) {
        throw new RuntimeException("Missing required frontmatter field [{$field}] in [{$path}].");
    }
}
```

That choice mattered more than adding a CMS first. It made the public rules explicit:

- a piece of content has one slug and one publication state;
- locale-aware routes can resolve against a predictable shape;
- canonical URLs and sitemap entries can be derived from the same normalized payload;
- archives stay free of drafts without hand-written exceptions.

The alternative would have been looser and more familiar: let controllers assume the fields they need, and patch the missing cases one by one. That path is cheaper for a week and worse for a year. It spreads the contract across views, controllers, and editorial habit instead of giving it one place to fail loudly.

## Result

The result is not flashy. It is structural.

The site can now move from a Markdown file to a public route, a canonical URL, a JSON-LD payload, and a sitemap entry without each layer inventing its own rules. That also made the later locale-prefixed routes safer, because the content layer already knew how to choose between English, French, and fallback behavior.

It also changed how I think about editorial tooling. A content system does not start when someone adds a rich editor. It starts when the repo becomes strict enough to tell the difference between "a note saved on disk" and "a public document with routing and metadata discipline".

This also connects to [Technical SEO, sitemaps, and structured data for commerce](/en/journal/technical-seo-sitemaps-and-structured-data-for-commerce), because stable metadata is what keeps editorial URLs and SEO surfaces aligned. It also echoes [Crown DP and the work of making a deployment pipeline honest](/en/case-studies/pipeline-deploiement-crown-dp), for the same reason: the system becomes easier to trust once its real state is easier to read.
