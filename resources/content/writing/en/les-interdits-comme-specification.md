---
title: Constraints can be part of the design system
slug: les-interdits-comme-specification
summary: In a jewelry design system, the most useful rules do not only describe what to build; they also explain what must stay forbidden to protect the visual identity.
status: published
published_at: 2026-03-18
updated_at: 2026-03-20
tags:
    - design-system
    - delivery
    - journal
seo_title: Constraints can be part of the design system
seo_description: In a jewelry design system, explicit constraints help Vue components and technical visual identity survive time, delivery pressure, and team changes.
category: journal
publication_type: journal
accent_tone: sun
schema: article
canonical: "{{site_url}}/en/journal/les-interdits-comme-specification"
ogImage: /images/og/les-interdits-comme-specification.jpg
---

The decisive moment does not happen when a Vue component is finally documented. It happens when the team also knows what not to do, even when nobody is available to repeat the original design decision inside a jewelry design system.

## Problem

Design-system documentation usually describes components, spacing, colors, and approved variants. That is necessary. It is not enough in projects where pages live for a long time, teams change, and no permanent designer is available to arbitrate every new page.

Without negative rules, the system stays too open. A homepage ends up showing a price that breaks the brand tone. A filled button appears because it felt more visible. A title turns bold while the whole typographic logic of the site depended on something quieter. Nobody necessarily worked badly. The boundary was simply never written down.

## Decision

The most useful decision is to document constraints with the same seriousness as positive rules.

Saying that a homepage hero does not show prices, that a filled button does not belong in a certain visual language, or that a heading must not become bold is not a minor stylistic preference. It is a way to make a design decision durable. The team no longer has to guess the original intent. It can rely on an explicit constraint.

The alternative looks more flexible but costs more over time. Without stated constraints, every new page silently reopens decisions that should have remained stable. Development, content, and design end up replaying the same micro-arbitrations, with slightly different results each time.

## Result

Well-written constraints make delivery lighter. They reduce the number of decisions that need to be revisited and protect the character of the site when production continues with a different team from one week to the next.

They also do something else that matters: they force a better reading of the brand or product. To write down what must not happen, the team has to understand what that choice would degrade. Documentation becomes sharper. It stops being a gallery of components and starts acting like a technical translation of editorial, visual, and commercial intent.

That is why I think of constraints as a form of negative specification. They say less "here is the expected rendering" than "here is what we refuse if we want the system to stay coherent." On a live project, that often protects quality better than a longer catalog of positive examples.

This article belongs next to [When a deployment succeeds without really succeeding](/en/journal/quand-un-deploiement-reussi-ne-lest-pas) for a reason that goes beyond design. In both cases, the useful work is the same: make something explicit before ambiguity gets a chance to settle inside the system.
