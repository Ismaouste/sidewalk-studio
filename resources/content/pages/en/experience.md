---
seo_title: Experience
seo_description: Professional experience across ecommerce, product data, technical SEO, business integrations, and modernization of live systems.
hero:
    eyebrow: Experience
    title: Projects and experience
    summary: Ecommerce developer based in Nancy. Four core contexts to read the work, the technical choices, and the way I ship.
thesis: "Ecommerce and business-platform developer. I build systems that sell or that run an operation, I keep them in production, and I keep changing them without ever taking the service down."
positioning:
    - Understand the existing system and its constraints quickly.
    - Make decisions with the right people and the right scope.
    - Ship without losing the readability of the system.
contexts:
    - HBJO ecommerce with catalog, stock, media, SEO, and product data.
    - Laravel, WordPress, WooCommerce, PrestaShop, Vue, ERP, and business connectors.
    - Redesigns, ongoing delivery, infrastructure incidents, and go-live work.
professional_sections:
    - title: Jewely Ecommerce
      eyebrow: Ecommerce developer — 2021-2026
      summary: Custom ecommerce CMS for jewelry and watch houses. One Laravel / Vue back end shared by several clients, an in-house ERP, a product PIM and marketing connectors.
      paragraphs:
          - Five years at Jewely / Flippad, from September 2021 to the end of August 2026.
          - '2026: I turned a simple ecommerce site into a custom CMS with one common back end. A feature is built once and reaches every client site.'
          - '2025: Crown-DP, built alone from start to finish. I integrated the design supplied by a Strasbourg agency, and everyone was happy with the result.'
          - 'Also for the houses: the Rolex space of Louis Julian and the Rolex Certified Pre-Owned space of Godechot-Pauliet, under NDA with Rolex. Other houses: Auberi.'
          - Large social media campaigns for jewelry and watch retailers, for brands such as Tudor and Repossi.
          - On the in-house PIM (Product Information Manager), I built the Python scraping and enrichment algorithms now powering +20 000 product entries, their images, and their videos across the HBJO scope.
          - On the commerce side, I designed and maintained the connectors between the ERP, the PIM, and merchant catalogs — automatic product creation and synchronization toward Google Merchant Center, Facebook Catalog, and the downstream marketing pipelines.
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
                - Shared core, client themes, business connectors, ongoing delivery, and coordination between ERP, catalog, and front-end surfaces.
                - Python scrape + enrichment algorithms on the HBJO PIM — +20 000 active product entries with media.
                - Automatic product creation + sync to Google Merchant Center, Facebook Catalog, and marketing pipelines.
                - 'Houses: Crown-DP, Louis Julian, Godechot-Pauliet, Auberi. Rolex spaces for Louis Julian and Godechot-Pauliet.'
    - title: Infrastructure
      eyebrow: Cross-cutting devops — 2024-2026
      summary: Docker Swarm deployment on AWS for the Jewely platform. Automatic pipeline through EventBridge, Lambda, and SSM, then hardening after a disk incident.
      paragraphs:
          - I automated the deploys, then made the pipeline report what actually happened rather than what it had been asked to do.
          - "A disk filled up and the deploy rolled back silently, reporting success. Afterwards I made the pipeline check the image the service really ended up running, catch a rollback instead of trusting the first green signal, and clear its own leftovers before they became the next incident."
      detail_groups:
          - title: Stack
            pills:
                - Docker Swarm
                - ECR
                - Lambda
                - SSM
                - EventBridge
            items:
                - Automated deployment, post-mortem work, preventive cleanup, and rollback checks.
    - title: Freelance — WordPress and ecommerce sites
      eyebrow: Full-stack developer — Before 2023
      summary: Building and maintaining WordPress, WooCommerce, and PrestaShop sites for SMB / micro-business clients — showcase sites, ecommerce, business plugins, and technical SEO.
      paragraphs:
          - "Before Jewely, I spent several years building and running WordPress and WooCommerce sites for small and mid-sized businesses: the build, custom plugins when the need went past what the platform offered, technical SEO, and maintenance over the long run."
      detail_groups:
          - title: Stack
            pills:
                - WordPress
                - WooCommerce
                - PrestaShop
                - PHP
                - Technical SEO
            items:
                - Showcase and ecommerce sites with custom business plugins.
                - Performance optimization, technical SEO, and long-term maintenance.
associative_sections:
    - title: Aremedia
      eyebrow: Nonprofit salaried — Before 2023
      summary: A salaried role in public health. I built them a self-hosted reporting tool for field teams and rebuilt the public site.
      paragraphs:
          - The people using it worked outside, often in one-off encounters, and the data was about health. That ruled out anything that needed a stable connection, a login they would forget, or a third-party server.
          - So I built them a self-hosted reporting tool they could use in the field, and rebuilt the public site at aremedia.org.
      detail_groups:
          - title: Landmarks
            items:
                - Harm reduction and outreach screening.
                - Self-hosted open survey under strong security and trust constraints.
                - Rework of the public site aremedia.org.
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
    - title: Commerce and product data
      summary: I keep catalog, stock, media, and URLs agreeing with each other across an ERP, a PIM, and a shop — currently for more than 20,000 products.
    - title: Laravel delivery
      summary: I work on Laravel platforms that are already serving customers, which means shipping changes without taking the shop down to do it.
    - title: Writing decisions down
      summary: When I choose between two approaches I write down which one and why, so the next person does not have to guess or ask me.
strengths:
    - I change platforms that are already selling, without interrupting sales.
    - "I work between disciplines: product data, search, privacy and the integrations between business tools."
    - I leave a system easier to understand than I found it.
focus_areas:
    - title: Laravel in production
      summary: Evolving Laravel platforms that serve customers every day, in stages, without interrupting the service.
    - title: Technical SEO and information architecture
      summary: URLs, metadata, structured data, and public content logic.
    - title: Consent and analytics
      summary: Pixels, data layers, and analytics handled inside a privacy-aware frame.
stack_groups:
    - title: Core stack
      items:
          - Laravel
          - PHP
          - Vue 3
          - Inertia
          - SQL
    - title: Typical environments
      items:
          - Ecommerce
          - CMS
          - ERP and connectors
          - Docker
    - title: Working methods
      items:
          - Framing
          - Incremental delivery
          - Documentation
          - SEO and privacy
career_snapshot:
    title: Technical snapshot
    summary: Laravel, Vue, WordPress, PrestaShop, WooCommerce, Docker, tracking, technical SEO, and product-data integrations.
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
        - Technical SEO
looking_for: Based in Nancy, available for a role or a focused mission in remote, hybrid, or on-site settings depending on the context.
hobbies:
    - Cycling and urbanism
    - Jazz and contemporary music
    - Independent cinema
    - Poetry and contemporary fiction
    - Urban photography
---
