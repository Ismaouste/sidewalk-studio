---
title: Content Systems Should Start With Routing and Metadata
slug: content-systems-routing-and-metadata
summary: A short note on why Markdown-driven publishing only becomes useful once slugs, metadata, and publication state are modeled explicitly.
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

That decision lets the application do three things safely:

- reject incomplete documents early
- expose stable URLs for sitemap and canonical generation
- separate draft content from public content without extra infrastructure

A content file is not just text. It is a contract between editorial intent and application behavior.
