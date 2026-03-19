---
title: Crown DP and the work of making a deployment pipeline honest
slug: pipeline-deploiement-crown-dp
summary: "An ecommerce case study about silent Docker Swarm rollbacks, misleading deployment signals, and the work of making the final state verifiable."
status: published
published_at: 2026-03-18
updated_at: 2026-03-20
tags:
    - deployment
    - operations
    - ecommerce
    - work-sample
seo_title: Crown DP and the work of making a deployment pipeline honest
seo_description: Ecommerce case study about a silent Docker Swarm rollback, a deployment pipeline that lied, and the hardening needed to make releases auditable again.
category: work
publication_type: case_study
accent_tone: dominant
schema: creative_work
canonical: "{{site_url}}/en/case-studies/pipeline-deploiement-crown-dp"
ogImage: /images/og/site-default.jpg
client: Jewely / Flippad · Crown DP
role: Incident analysis, deployment hardening, operational stabilization
stack:
    - Docker Swarm
    - Laravel
    - PHP
    - CI / CD
    - Bash
outcomes:
    - Detect silent rollbacks
    - Verify the image actually running in production
    - Reduce failures caused by disk pressure
---

The pipeline already existed. It ran the right commands and looked healthy at first glance. The problem was that it could still lie. A `docker service update` could return exit `0` while Docker Swarm silently rolled the service back to the previous image. In the CI log, the fix looked delivered. In production, nothing had really changed.

In ecommerce, that kind of false positive is expensive. A catalog correction, a checkout fix, or a content update gets announced as shipped while the live service keeps running the old image. The issue is not only operational. It erodes trust in the tooling and in the signals teams use to coordinate releases.

## Situation

This happened in a live Crown DP environment with branches, preproduction surfaces, and real timing pressure. The goal was not to rebuild the whole delivery chain around a cleaner tool. The immediate need was to understand why the success signal was unreliable and to put a simple, auditable decision path back around the actual state of the service.

Two checks became central:

- verify the digest or image reference actually retained by the service after deployment, not only the command exit code;
- surface rollback states instead of letting Docker Swarm absorb them without making the job fail loudly.

## Decision

The useful decision was not to replace the pipeline. It was to make it honest.

That meant:

- comparing the expected image with the image finally retained by the service;
- detecting rollback conditions after the update instead of trusting the first green signal;
- cleaning some infrastructure residue earlier, especially around disk pressure;
- documenting the observed behavior so the team could understand the failure mode without replaying the incident from scratch.

That choice matters because it respects context. On many real projects, the right answer is not to swap an imperfect pipeline for a theoretically perfect one. It is to harden the existing path until it stops hiding the real state of the system. That also meant looking at image retention, because an imprecise ECR lifecycle policy lets the same fragility come back through storage pressure and stale images.

## Result

The first result was not visual. It was operational. The pipeline stopped being a sequence of commands and started acting again as a reliable witness of the final deployed state.

When a deployment succeeds, the team can believe it. When it fails, the signal is narrower and more useful: rollback detected, stale image retained, or environment pressure to resolve first. That shortens the loop between CI, servers, and production and removes a lot of nervous, low-value verification work.

This is also representative of the kind of delivery work I care about. I am less interested in showing a stack than in finding the place where a system lies, drifts, or makes teams think they still control a release when they no longer do. Once that place is explicit, the most useful change is often smaller than a refactor, but much more valuable in day-to-day operation.
