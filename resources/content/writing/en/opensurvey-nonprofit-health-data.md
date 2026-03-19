---
title: Open survey, non-profit work, and health data constraints
slug: opensurvey-nonprofit-health-data
summary: A reporting tool for Aremedia had to stay sober, understandable, and secure enough for health-related field work without becoming a heavy platform.
status: published
published_at: 2026-03-06
updated_at: 2026-03-19
tags:
    - opensurvey
    - health
    - nonprofit
    - notes-dev
seo_title: Open survey, non-profit work, and health data constraints
seo_description: Aremedia needed a reporting tool that respected health-data constraints, field usage, and sober hosting without importing needless complexity.
category: journal
publication_type: note
accent_tone: violet
schema: article
canonical: "{{site_url}}/en/journal/opensurvey-nonprofit-health-data"
ogImage: /images/og/opensurvey-nonprofit-health-data.jpg
---

This project was never "just another form". The useful question was whether a reporting tool could stay readable enough for a nonprofit team while carrying stronger constraints than many ordinary business tools.

## Problem

The Aremedia context combined several pressures at once:

- field usage outside office routines;
- data that touched public-health realities and therefore could not be treated casually;
- a need for infrastructure choices that remained explainable to the people running the project;
- very little room for digital theater.

That last point matters. Nonprofit tooling often gets pushed toward two bad extremes: either generic SaaS used far beyond its comfort zone, or a custom platform so overbuilt that the team becomes dependent on specialist maintenance from day one.

## Decision

The decision here was to keep the stack sober and the hosting understandable.

That meant accepting a simpler product surface while being stricter about the few things that actually mattered:

- who has access to the data;
- where the data lives;
- how the tool behaves in field conditions;
- how the system can be maintained without guesswork.

Self-hosting stayed on the table not because it sounded virtuous, but because it supported those constraints better than an opaque third-party chain. At the same time, the security-sensitive parts were not romanticized. External expertise remained useful where specialist review was the responsible choice.

The alternative would have been familiar: adopt a smoother-looking cloud stack, accumulate integrations, and postpone the hard questions about data handling and long-term operation. That might have looked easier in the first month. It would not have made the system more trustworthy.

## Result

The result was a tool that stayed proportionate to the organization using it.

It did not try to imitate a startup product. It tried to support actual work: teams collecting information in the field, an organization needing reliable reporting, and a technical base that remained understandable enough to document and operate. That is a better success condition than visual sophistication.

I find this kind of work useful because it reorders the usual priorities. Instead of asking how much product polish can be layered on top, the project starts by asking whether the organization can still understand the system when something breaks, when someone leaves, or when the context changes. That is a harder question, and usually a better one.

This is also why the article belongs next to [Linux, self-hosting, and practical alternatives](/en/journal/linux-self-hosting-and-practical-alternatives). Both pieces are really about the same question: when does control help, and when does it become unnecessary weight? It also extends the case study [Self-hosted nonprofit tooling for sensitive data](/en/case-studies/self-hosted-nonprofit-tooling-for-sensitive-data), where the hosting choice becomes inseparable from the product constraints.
