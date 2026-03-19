---
title: When a deployment succeeds without really succeeding
slug: quand-un-deploiement-reussi-ne-lest-pas
summary: A silent Docker Swarm rollback can leave production on the old image while an automated deployment still reports success.
status: published
published_at: 2026-03-18
updated_at: 2026-03-20
tags:
    - deployment
    - reliability
    - journal
seo_title: When a deployment succeeds without really succeeding
seo_description: Notes on a silent Docker Swarm rollback, a misleading automated deployment, and why image retention and ECR lifecycle policy became part of the fix.
category: journal
publication_type: journal
accent_tone: dominant
schema: article
canonical: "{{site_url}}/en/journal/quand-un-deploiement-reussi-ne-lest-pas"
ogImage: /images/og/quand-un-deploiement-reussi-ne-lest-pas.jpg
---

The problem did not start in application code. It started the moment an automated deployment reported success while a silent Docker Swarm rollback sometimes left production on the previous image.

## Problem

The client-side case was easy to summarize and annoying to diagnose. A `docker service update` completed without visible noise, the CI job stayed green, and the service could still settle back on the old image after an automatic rollback. Reading the pipeline suggested the fix had shipped. Reading production suggested nothing had moved.

That kind of false positive is costly because it damages the reliability of the signal itself. Teams start debugging in the wrong place, or they communicate a release that never actually happened. In a live ecommerce context, that means catalog corrections, front-end fixes, or checkout work can miss production while everybody believes the issue is already closed.

## Decision

The useful decision was not to rebuild the deployment chain from scratch. It was to make the pipeline honest about the final state of the service.

In practice, that meant checking the image retained after deployment rather than trusting the command return alone:

```bash
expected_image="$REGISTRY/$APP_IMAGE:$IMAGE_TAG"
service_name="$APP_SERVICE"

docker service update \
  --with-registry-auth \
  --image "$expected_image" \
  "$service_name"

current_image="$(docker service inspect "$service_name" \
  --format '{{.Spec.TaskTemplate.ContainerSpec.Image}}')"

if [[ "$current_image" != *"$IMAGE_TAG"* ]]; then
  echo "Rollback or stale image detected: $current_image"
  exit 1
fi
```

The second part of the decision was to stop treating every symptom as a pure CI concern. Disk pressure and image retention were part of the same failure story. That is why the fix also touched operational hygiene and raised the question of what an ECR lifecycle policy should keep, or remove, between deployments. The alternative would have been to keep the same green signal and add more manual checks around it. That would have shifted the problem, not solved it.

## Result

The first result was operational, not visual. The pipeline stopped being a simple chain of commands and became a more reliable witness of the final deployed state.

In ecommerce, that matters because teams do not have an hour to wonder whether the right catalog fix or checkout patch is truly live. A useful pipeline is not an impressive one. It is a pipeline that makes reality easier to read.

I like this kind of correction because it puts the work back at the right scale. The goal was not to build a more spectacular release chain. The goal was to reduce the gap between the signal on screen and the state really running in production. A large part of reliability work lives exactly there.

This note extends the case study [Making a deployment pipeline honest in a live ecommerce environment](/en/case-studies/pipeline-deploiement-ecommerce), where the same problem and hardening work are described at project scale. It also belongs next to [Content systems start with routing and metadata](/en/journal/content-systems-routing-and-metadata), because both topics are really about the same deeper issue: reducing the gap between what a system says and what it actually does.
