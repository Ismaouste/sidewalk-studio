---
title: Rendre un pipeline de déploiement honnête en environnement e-commerce
slug: pipeline-deploiement-ecommerce
translation_key: pipeline-deploiement-ecommerce
summary: "Un case study e-commerce sur un Docker Swarm rollback silencieux, un déploiement automatique trompeur et le besoin de rendre l'état final vérifiable."
status: published
published_at: 2026-03-18
updated_at: 2026-03-19
tags:
    - deployment
    - operations
    - ecommerce
    - work-sample
seo_title: Rendre un pipeline de déploiement honnête en environnement e-commerce
seo_description: Case study e-commerce sur un Docker Swarm rollback silencieux, un déploiement automatique trompeur et un durcissement progressif avec gestion plus propre des images.
category: work
publication_type: reference
accent_tone: dominant
schema: creative_work
canonical: "{{site_url}}/fr/case-studies/pipeline-deploiement-ecommerce"
ogImage: /images/og/site-default.jpg
client: Jewely / Flippad
role: Analyse incident et stabilisation du déploiement
stack:
    - Docker Swarm
    - Laravel
    - PHP
    - CI / CD
    - Bash
outcomes:
    - Détecter les rollbacks silencieux
    - Vérifier l'image réellement en production
    - Réduire les échecs liés à la pression disque
---

Le problème n'était pas un pipeline absent. Il existait, il exécutait les bonnes commandes et il donnait même le bon signal au premier regard. Le souci était plus pervers : un `docker service update` pouvait se terminer avec un code de sortie propre alors qu'un Docker Swarm rollback automatique venait en réalité de remettre l'ancienne image en place. À la lecture du job CI, tout semblait vert. En production, rien n'avait vraiment bougé.

Dans un contexte ecommerce, ce type de faux positif coûte cher. On pense avoir livré un correctif, un contenu, une évolution de catalogue ou une réparation urgente. On communique éventuellement que c'est fait. Puis on découvre plus tard que le site tourne encore sur l'image précédente. Le problème n'est pas seulement technique. C'est un problème de confiance dans l'outillage.

## Situation

Le cas arrivait dans un environnement client déjà vivant, avec ses branches, ses préproductions et ses contraintes de timing. Le but n'était pas de reconstruire une usine à gaz autour du déploiement. Il fallait d'abord comprendre pourquoi le signal de succès mentait, puis remettre une chaîne de décision simple autour de l'état réel du service.

Deux angles se sont imposés rapidement :

- vérifier le digest réellement servi après le déploiement, pas seulement l'exécution de la commande ;
- rendre visibles les rollbacks et les états d'échec que Docker Swarm pouvait absorber sans faire échouer explicitement le job.

## Décision

La décision n'a pas été de changer complètement d'outil. Elle a été de rendre le pipeline plus honnête.

Concrètement, cela a consisté à :

- comparer l'image attendue et l'image effectivement retenue par le service après l'update ;
- détecter le rollback final au lieu de s'arrêter au premier succès apparent ;
- nettoyer préventivement certains résidus pour limiter les échecs dus à la pression disque ;
- documenter le comportement observé pour que le problème soit compréhensible par l'équipe sans rejouer tout l'incident.

Ce choix est important parce qu'il respecte le contexte. Dans beaucoup de projets, la bonne décision n'est pas de remplacer l'existant par un pipeline parfait sur le papier. C'est de fiabiliser ce qui existe déjà, en supprimant les angles morts qui coûtent des heures de diagnostic. Cela supposait aussi de regarder la politique de rétention des images, parce qu'une ECR lifecycle policy trop vague laisse revenir les mêmes fragilités sous une autre forme.

## Résultat

Le premier résultat n'a pas été visuel. Il a été opérationnel. Le pipeline est passé d'un rôle purement exécutant à un rôle de témoin fiable de l'état final.

Quand un déploiement réussit, on peut désormais le croire. Quand il échoue, l'équipe dispose d'un signal plus net sur la cause probable : rollback silencieux, image finale incohérente ou environnement à nettoyer. Cela change beaucoup de choses dans la pratique quotidienne, parce que les allers-retours entre CI, serveur et production deviennent plus courts, plus auditables et moins nerveux.

Ce type de travail raconte assez bien ma façon d'aborder un sujet de delivery. Je ne cherche pas d'abord à montrer une stack ou un outil. Je cherche à comprendre où le système ment, où il fatigue, où il laisse l'équipe croire qu'elle maîtrise encore la situation alors que ce n'est plus tout à fait vrai. Une fois cet endroit trouvé, le bon changement est souvent plus sobre qu'on l'imagine, mais beaucoup plus utile qu'une refonte totale.
