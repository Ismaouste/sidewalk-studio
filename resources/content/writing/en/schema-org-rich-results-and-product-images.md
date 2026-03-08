---
title: Schema.org, rich results, and product images
slug: schema-org-rich-results-and-product-images
summary: "A note on e-commerce structured data, product pages, collection pages, organization markup, and one annoying detail: Google does not always like its own WebP format."
status: published
published_at: 2026-03-08
updated_at: 2026-03-08
tags:
    - schema-org
    - seo
    - structured-data
    - notes-dev
seo_title: Schema.org, rich results, and product images
seo_description: Technical note on e-commerce structured data, product pages, collection pages, local entities, and the real constraints behind Google rich results.
category: note
publication_type: note
accent_tone: violet
---

In e-commerce, structured data is never just about dropping a `Product` node onto a product page and calling it done.

In HBJOAT contexts, the real work often sits in making several layers hold together at once:

- the product page with price, availability, image, and offer data;
- listing or collection pages;
- brand, organization, or store pages;
- broader entities such as return policy, opening hours, local business data, or reimbursement-related information when it exists.

On paper, `schema.org` makes this all look neatly stackable. In practice, the real question is what remains maintainable, what matches the actual HTML, and what still has a realistic chance of producing useful Google enhancements.

I also remember a more industrial case around Prudhomme Transmissions and 3D files. The format looked interesting, but the cleanest delivery path ended up being a free downloadable product rather than an ambitious promise poorly supported by markup.

The fun fact that stayed with me concerns product images. When a rich result appears on mobile with price, stock signal, and product image, you might expect Google to naturally favor its own WebP format. In practice, JPEG and PNG often remain the safer path for stable rendering.

That is the rule I keep coming back to: structured data matters most when it stays readable, reliable, and compatible with the reality of the catalog and its assets.
