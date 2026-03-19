---
title: Linux, self-hosting, and practical alternatives
slug: linux-self-hosting-and-practical-alternatives
summary: Linux and self-hosting are useful when they reduce dependency and keep the system readable, not when they turn maintenance into a side job.
status: draft
published_at: 2026-03-08
updated_at: 2026-03-19
tags:
    - linux
    - self-hosting
    - framasoft
    - journal
seo_title: Linux, self-hosting, and practical alternatives
seo_description: Linux and self-hosting matter when they reduce dependency, keep infrastructure readable, and stay realistic for teams that still have work to do.
category: journal
publication_type: journal
accent_tone: violet
schema: article
canonical: "{{site_url}}/en/journal/linux-self-hosting-and-practical-alternatives"
ogImage: /images/og/linux-self-hosting-and-practical-alternatives.jpg
---

The useful question is rarely "should everything be self-hosted?" It is closer to this: where does a hosted service remove work, and where does it hide a dependency that will hurt later?

## Problem

Linux and self-hosting attract a lot of moral language. In practice, the tension is operational.

In small associations, independent projects, and modest business contexts, the same pattern comes back:

- a dominant SaaS is convenient until pricing, lock-in, or data location becomes a problem;
- a self-hosted alternative exists, but somebody still has to patch it, monitor it, and explain it to the next person;
- the team wants autonomy, but it does not want a second job disguised as infrastructure.

That is why I no longer think of Linux or self-hosting as identity markers. They are delivery choices. A Debian server, a self-hosted service, or a Framasoft replacement only makes sense if the maintenance burden stays proportional to the value returned.

This matters even more when the context includes field work, nonprofit budgets, or sensitive data. The wrong answer is not only expensive. It can quietly produce a system nobody feels able to touch six months later.

## Decision

The decision I keep coming back to is to separate three cases instead of treating them as one ideological block.

First, some needs deserve a hosted service because the operational cost is not where the project should spend its energy. That is especially true when the feature is peripheral and the hosting provider is transparent enough about backups, permissions, and export paths.

Second, some needs justify self-hosting because control is part of the requirement. That can mean data sensitivity, budget stability, local integration constraints, or simply the need to understand how the system actually works.

Third, there is a useful middle ground: services that are not self-hosted by the project team, but still come from ecosystems that preserve interoperability and sane defaults. Framasoft has often been a good example of that practical middle line in French nonprofit contexts. It keeps the discussion grounded. The alternative to a dominant platform is not always "run your own cluster". Sometimes it is "choose a provider whose model and tooling remain legible".

The alternative would be to collapse everything into one slogan. "Self-host everything" sounds pure and often creates fragile maintenance. "Just use the mainstream stack" sounds practical and often imports dependencies the team never really assessed. Both shortcuts skip the real work, which is to map the cost of control against the cost of delegation.

## Result

This way of looking at the topic changed my technical choices and my conversations with teams.

It becomes easier to explain why one service can stay hosted, why another should live on an understandable Linux box, and why documentation matters as much as the runtime itself. A good self-hosted setup is not defined by heroics. It is defined by whether the next person can still operate it without guessing.

That is also why I keep linking the topic back to [Open survey, non-profit work, and health data constraints](/en/journal/opensurvey-nonprofit-health-data). The question there was never "can we self-host?" in the abstract. It was "can we keep the system sober, understandable, and proportionate to the risk?" It also relates to the case study [Self-hosted nonprofit tooling for sensitive data](/en/case-studies/self-hosted-nonprofit-tooling-for-sensitive-data), where hosting choice becomes part of the product decision rather than an ops afterthought.

I still like Linux for the same reason I liked it in the first place: it keeps more of the system visible. But visibility only becomes useful when it helps a real team make calmer decisions.
