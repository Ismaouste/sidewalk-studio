---
seo_title: Parcours de tech lead e-commerce
seo_description: Parcours de tech lead e-commerce à Nancy, Grand Est, entre données produit, intégrations métier, SEO technique et environnements vivants.
hero:
    eyebrow: Expérience
    title: Projets et expérience
    summary: Développeur e-commerce à Nancy. Quatre contextes principaux pour lire le travail, les choix techniques et la façon de livrer.
thesis: "Développeur e-commerce et plateformes métier. Je construis des systèmes qui vendent ou qui font tourner une activité, je les tiens en production, et je les fais évoluer sans jamais couper le service."
positioning:
    - Comprendre vite l'existant et ses contraintes.
    - Décider proprement avec les bons interlocuteurs.
    - Livrer sans perdre la lisibilité du système.
contexts:
    - Ecommerce HBJO avec catalogue, stock, médias, SEO et donnée produit.
    - Laravel, WordPress, WooCommerce, PrestaShop, Vue, ERP et connecteurs métier.
    - Refontes, delivery continu, incidents d'infrastructure et mises en ligne.
professional_sections:
    - title: Jewely E-commerce
      eyebrow: Développeur e-commerce — 2023-2026
      summary: Plateforme e-commerce multi-tenant pour maisons HBJO. Socle Laravel / Vue partagé, ERP maison, PIM produit custom, connecteurs marketing et delivery continu.
      paragraphs:
          - Chez Jewely / Flippad, j'interviens sur le socle commun (Laravel + Vue + Inertia), sur les besoins spécifiques par maison et sur les chantiers transversaux qui touchent à la donnée produit, au front, aux connecteurs métier et à la mise en ligne.
          - 'Périmètre HBJO premium avec marques principales en portefeuille — notamment Godechot-Pauliet, Auberi et Crown-DP — ainsi que des dispositifs Rolex Bespoke et Rolex Certified Pre-Owned.'
          - Sur le PIM (Product Information Manager) maison, j'ai construit les algos Python de scrap et d'enrichissement qui alimentent aujourd'hui +20 000 fiches produit, leurs images et leurs vidéos sur le périmètre HBJO.
          - "Côté commerce, j'ai conçu et tenu les connecteurs entre l'ERP, le PIM et les catalogues marchand : création produit automatique et orchestration de synchronisation vers Google Merchant Center, Facebook Catalog et les pipelines marketing aval."
      detail_groups:
          - title: Stack
            pills:
                - Laravel
                - Vue 3
                - Inertia
                - Python
                - Docker Swarm
                - AWS
            items:
                - Core partagé, thèmes par client, connecteurs métier, delivery continu et coordination entre ERP, catalogue et front.
                - Algos Python de scrap + enrichissement PIM HBJO — +20 000 fiches produit avec médias actives.
                - Création produit auto + sync Google Merchant Center, Facebook Catalog, pipelines marketing.
                - Cadre HBJO premium — Godechot-Pauliet, Auberi, Crown-DP, Rolex Bespoke, Rolex Certified Pre-Owned.
    - title: Infrastructure
      eyebrow: Devops transversal — 2024-2026
      summary: Déploiement Docker Swarm sur AWS pour la plateforme Jewely. Pipeline automatique via EventBridge, Lambda et SSM, puis durcissement après incident disque.
      paragraphs:
          - J'ai automatisé les déploiements, puis j'ai fait en sorte que le pipeline rende compte de ce qui s'est réellement passé, et non de ce qu'on lui avait demandé de faire.
          - "Un disque s'est rempli et le déploiement est revenu en arrière sans le dire, en annonçant un succès. Ensuite, j'ai fait vérifier au pipeline quelle image tournait vraiment, détecter un retour en arrière au lieu de croire le premier feu vert, et nettoyer ses propres résidus avant qu'ils deviennent l'incident suivant."
      detail_groups:
          - title: Stack
            pills:
                - Docker Swarm
                - ECR
                - Lambda
                - SSM
                - EventBridge
            items:
                - Déploiement automatique, post-mortem, nettoyage préventif et contrôle des rollbacks.
    - title: Freelance — Sites WordPress et e-commerce
      eyebrow: Développeur full-stack — Avant 2023
      summary: Réalisation et maintenance de sites WordPress, WooCommerce et PrestaShop pour des clients TPE/PME — vitrines, e-commerce, plugins métier et SEO technique.
      paragraphs:
          - "Avant Jewely, j'ai construit et fait vivre pendant plusieurs années des sites WordPress et WooCommerce pour des TPE et des PME : l'intégration, les plugins métier quand le besoin sortait du standard, le référencement, et la maintenance dans la durée."
      detail_groups:
          - title: Stack
            pills:
                - WordPress
                - WooCommerce
                - PrestaShop
                - PHP
                - SEO technique
            items:
                - Sites vitrines et e-commerce avec personnalisations métier.
                - Optimisation performance, SEO technique et maintenance long terme.
associative_sections:
    - title: Aremedia
      eyebrow: Salarié associatif — Avant 2023
      summary: Poste salarié en santé publique. J'y ai construit un outil auto-hébergé pour les équipes de terrain et refait le site public.
      paragraphs:
          - Les personnes qui s'en servaient travaillaient dehors, souvent lors de rencontres uniques, et la donnée touchait à la santé. Ça excluait tout ce qui demandait une connexion stable, un mot de passe qu'on oublie, ou un serveur chez un tiers.
          - Je leur ai donc construit un outil de remontée de données auto-hébergé, utilisable sur le terrain, et j'ai repris le site public aremedia.org.
      detail_groups:
          - title: Repères
            items:
                - Prévention des risques et dépistage hors les murs.
                - Open survey auto-hébergé avec contraintes de sécurité et de confiance.
                - Reprise du site public aremedia.org.
associative_note_widget:
    eyebrow: ''
    title: ''
    description: ''
    cta_label: ''
side_project_sections: []
side_projects_widget:
    eyebrow: ''
    title: ''
    description: ''
    cta_label: ''
trajectory:
    - title: Commerce et données produit
      summary: Je fais en sorte que le catalogue, le stock, les médias et les URLs disent la même chose entre un ERP, un PIM et une boutique — aujourd'hui sur plus de 20 000 produits.
    - title: Livraison Laravel
      summary: Je travaille sur des plateformes Laravel qui servent déjà des clients, donc je livre des changements sans avoir à fermer la boutique pour le faire.
    - title: Écrire les décisions
      summary: Quand je tranche entre deux approches, j'écris laquelle et pourquoi, pour que la personne suivante n'ait ni à deviner ni à me le demander.
strengths:
    - Je fais évoluer des plateformes qui vendent déjà, sans jamais couper la vente.
    - Je travaille sur les jointures — donnée produit, référencement, vie privée, intégrations entre outils métier — plutôt qu'à l'intérieur d'une seule.
    - Je laisse un système plus lisible que je ne l'ai trouvé, et c'est la partie qui m'intéresse le plus.
focus_areas:
    - title: Laravel en production
      summary: Faire évoluer par étapes des plateformes Laravel qui servent des clients tous les jours, sans interrompre le service.
    - title: SEO technique et architecture de l'information
      summary: URLs, métadonnées, données structurées et logique de contenu public.
    - title: Consentement et analytics
      summary: Pixels, data layer et analytics respectueux du cadre RGPD.
stack_groups:
    - title: Stack cœur
      items:
          - Laravel
          - PHP
          - Vue 3
          - Inertia
          - SQL
    - title: Environnements typiques
      items:
          - E-commerce
          - CMS
          - ERP et connecteurs
          - Docker
    - title: Méthodes de travail
      items:
          - Cadrage
          - Livraison incrémentale
          - Documentation
          - SEO et RGPD
career_snapshot:
    title: Repères techniques
    summary: Laravel, Vue, WordPress, PrestaShop, WooCommerce, Docker, tracking, SEO technique et intégrations produit.
    roles:
        - Laravel
        - Vue 3
        - WordPress
        - WooCommerce
        - PrestaShop
        - Docker
        - AWS
        - GTM
        - Adobe Analytics
        - SEO technique
looking_for: Basé à Nancy, disponible pour un poste ou une mission ciblée en remote, hybride ou sur site selon le contexte.
hobbies:
    - Vélo et urbanisme
    - Jazz et musique contemporaine
    - Cinéma indépendant
    - Poésie et fiction contemporaine
    - Photographie urbaine
---
