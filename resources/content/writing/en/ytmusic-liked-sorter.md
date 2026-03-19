---
title: YTMusic Liked Sorter
slug: ytmusic-liked-sorter
summary: A small personal utility became useful once it turned a long like history into something sortable, readable, and worth revisiting again.
status: published
published_at: 2026-03-07
updated_at: 2026-03-19
tags:
    - ytmusic-liked-sorter
    - notes-dev
    - hobby
seo_title: YTMusic Liked Sorter
seo_description: YTMusic Liked Sorter is a small utility for turning a noisy YouTube like history into something sortable and usable again.
category: note
publication_type: note
accent_tone: coral
schema: article
canonical: "{{site_url}}/en/journal/ytmusic-liked-sorter"
ogImage: /images/og/ytmusic-liked-sorter.jpg
---

The starting point was simple: too much liked music, no useful way to revisit it, and no desire to turn a personal archive into another endless interface.

## Problem

Platform histories are good at accumulation and bad at retrieval. Once the liked list grows, the archive becomes technically present but practically lost. Search is too narrow, browsing is too blunt, and the user ends up remembering that something existed without any realistic path back to it.

That is the sort of problem I like because it is ordinary and honest. No market study is required. The friction is visible, the use case is real, and the expected result is clear: recover signal from volume.

## Decision

The decision was to keep the tool narrow.

It needed three things:

- a way to ingest an existing history;
- a way to sort and filter it more usefully than the default interface;
- an output that stays readable instead of becoming another pile of data.

That is enough for a good utility. Anything beyond that would have pushed the project toward product theater. The repository is public on [GitHub](https://github.com/Ismaouste/ytmusic-liked-sorter) for the same reason: the interesting part is the clarity of the retrieval flow, not a closed polished shell around it.

The alternative would have been to overbuild it. Add sharing, accounts, sync, or a broader product pitch. That would have made the project less truthful. The value here comes from a tight relationship between the inconvenience and the code that removes it.

## Result

The result is a calmer way to revisit a history that had become too noisy to use. That may sound minor, but it is exactly the right scale for the problem.

I like small tools like this because they keep technical judgment close to everyday use. They ask the same questions as bigger systems in miniature: where is the real friction, which data matters, and what is the smallest interface that makes the result useful again?

That is also what keeps the project honest. Nobody needs a grand narrative to justify it. If the history becomes easier to revisit, the tool has already done enough. That kind of bounded success is rare, and worth protecting.

That is why this note belongs near [NJP volunteering and small useful tools](/en/journal/njp-volunteering-and-small-tools). Both pieces are really about the same habit: stay close to a concrete inconvenience and let the scope remain human-sized. It is also why small personal tools still say something about larger engineering work. They reward the same restraint that keeps bigger systems from becoming heavier than the problem they solve.
