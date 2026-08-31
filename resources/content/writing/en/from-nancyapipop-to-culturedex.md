---
title: From nancyapipop to Culturedex
slug: from-nancyapipop-to-culturedex
translation_key: from-nancyapipop-to-culturedex
summary: Two public-data projects around the French POP API, from a Nancy prototype to a more structured backend for exploring cultural records.
status: published
published_at: 2026-03-08
updated_at: 2026-03-19
tags:
    - pop-api
    - nestjs
    - public-data
    - cultural-data
seo_title: From nancyapipop to Culturedex
seo_description: Two POP API projects, from a Nancy prototype to a more structured backend for making cultural records easier to browse and reuse.
category: journal
publication_type: journal
accent_tone: violet
schema: article
canonical: "{{site_url}}/en/journal/from-nancyapipop-to-culturedex"
ogImage: /images/og/from-nancyapipop-to-culturedex.jpg
---

The first useful result was not a polished interface. It was a filtered view of the POP API that finally answered a simple question: what can I actually retrieve around Nancy without getting lost in the source vocabulary?

## Problem

Public datasets often fail at the last meter. The data exists, the API exists, the documentation exists, and yet the path from source to actual use remains awkward.

That was the situation with the French Ministry of Culture POP API. The source was rich, but the practical work was elsewhere:

- understanding which filters were really useful for one territory;
- learning the vocabulary embedded in the API responses;
- figuring out what could become a readable browsing experience instead of a raw response dump.

`nancyapipop` was the first answer. It stayed close to the prototype stage and focused on something modest but necessary: getting the Nancy-specific filtering logic under control. That work mattered because it reduced the distance between "the API is available" and "someone can actually use the result".

## Decision

The decision was not to jump directly into a bigger platform. It was to split the work into two phases.

First, `nancyapipop` stayed intentionally close to the problem. The goal was to understand the input surface: filters, fields, limitations, and the shape of the useful queries.

Then `culturedex` picked up the same intent with more structure. The project moved into a NestJS backend, cleaner TypeScript organization, and a more explicit browsing ambition. That second step only made sense because the first project had already reduced uncertainty around the source itself.

I like this sequence because it avoids a common mistake in public-data work: building architecture before the vocabulary is understood. The alternative would have been to start with a bigger technical shell and only later discover that the real difficulty lived in the filters, the mapping, or the way the source described cultural records. That usually produces a cleaner codebase with a weaker product.

## Result

The result is two related artifacts with different jobs.

`nancyapipop` proved that a small territorial prototype could already make the POP API more readable. `culturedex` pushed the same direction further, with a backend structure that made the work easier to extend, document, and test. Together they show the pattern I keep returning to in other contexts as well: start by learning the real shape of the source, then decide how much architecture the problem actually deserves.

These projects do not point directly to ecommerce. They still say something useful about the way I work on data surfaces. Whether the input is a public cultural API or a product feed, the first task is often the same: reduce ambiguity, understand the vocabulary, then build a layer that makes the source usable for someone other than the person who read the raw payload.

That is also why this article belongs near [Content systems start with routing and metadata](/en/journal/content-systems-routing-and-metadata). In both cases, the useful work starts when a raw source becomes legible enough to support stable decisions. And because the work stayed grounded in place, it also belongs near [Local](/en/local) even if the implementation path remained technical first.
