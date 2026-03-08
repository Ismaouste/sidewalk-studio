---
title: Content Systems Should Start With Routing and Metadata
slug: content-systems-routing-and-metadata
summary: Markdown only becomes a usable publishing system once routing, metadata, and publication state are treated as part of the application contract rather than as editorial afterthoughts.
status: published
published_at: 2026-03-07
updated_at: 2026-03-07
tags:
    - content
    - seo
    - laravel
seo_title: Content Systems Should Start With Routing and Metadata
seo_description: Markdown is only the storage layer. The real system starts with routing, publication state, and metadata discipline.
---

Markdown is the easy part.

The hard part is deciding what makes a piece of content publishable. For Sidewalk Studio, that baseline is explicit frontmatter: title, slug, summary, publication state, timestamps, tags, and SEO metadata.

That sounds procedural, but it changes the engineering conversation immediately. The moment a page archive, canonical URL, or locale fallback depends on content shape, "just put it in Markdown" stops being a serious answer.

That decision lets the application do three things safely:

- reject incomplete documents early
- expose stable URLs for sitemap and canonical generation
- separate draft content from public content without extra infrastructure

## Why this matters in a Laravel and Inertia app

Once the frontend renders public archives and detail pages, content stops being a static file concern.

The application needs to know:

- whether a document is public
- which canonical path it owns
- which metadata belongs in the first response
- how locale fallback should behave when translated content is incomplete

That is why the content contract has to start before the editorial volume grows.

## What this proved

A content file is not just text. It is a contract between editorial intent and application behavior.

That framing made later features easier:

- case studies and writing could share one repository pattern
- locale fallback could be added without rewriting every content consumer
- public proof pages could stay honest because draft or incomplete documents never leaked into the runtime

It also made the content surface easier to defend in interviews and reviews: the repo is not proving that Markdown exists, it is proving that editorial input can participate safely in routing, SEO, and release discipline.
