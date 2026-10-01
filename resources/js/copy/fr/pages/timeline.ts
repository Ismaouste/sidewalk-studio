/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/timeline').default;

export default {
    caption:
        'Les projets et les postes sur un axe du temps, ce que j’ai utilisé, et où cela se raconte. Survolez une case, ou touchez-la, pour le détail.',
    columns: {
        notions: 'Technologies et notions',
        pages: 'Pages et écrits',
        projects: 'Projets et postes',
    },
    items: {
        aremedia: {
            detail: {
                period: '2020–2021',
                role: 'Un outil de remontée de données auto-hébergé pour des équipes de terrain en santé publique, et la refonte du site public.',
            },
            label: 'Aremedia',
            note: 'Santé publique : un outil de terrain auto-hébergé et le site public',
        },
        artem: {
            detail: {
                period: '2016–2017',
                role: 'Médiation entre institutions et organisations partenaires. Une journée de sensibilisation au développement durable pour 170 élèves et 15 partenaires.',
            },
            label: 'Alliance Artem',
            note: 'Médiateur, 2016–2017',
        },
        astralmanach: {
            detail: {
                figures: [
                    { label: 'sources', value: '34' },
                    { label: 'outils', value: '3' },
                ],
                links: [
                    {
                        href: 'https://www.npmjs.com/package/astralmanach',
                        label: 'npm',
                    },
                    {
                        href: 'https://astralmanach.eu/api-publique',
                        label: 'API publique',
                    },
                ],
                period: '2026',
                role: 'Le ciel d’une date, d’une heure et d’un lieu, en données : une bibliothèque npm sous licence MIT, une API publique en lecture seule et trois outils.',
            },
            label: 'astralmanach',
            note: 'Le ciel d’une date, en données',
        },
        astro: {
            detail: {
                role: 'La Lune, les planètes, les éclipses et la station spatiale pour une date, une heure et un lieu, calculées plutôt que recopiées.',
            },
            label: 'Calculs\nastronomiques',
            note: 'Le ciel en données',
        },
        atlas: {
            detail: {
                period: '2026',
                role: 'Une app, deux marques qui ne travaillent pas avec les artisans de la même façon : en sous-traitance ou en partenariat. Conception et développement.',
            },
            label: 'DD le Dépanneur,\nAtlas Dépannage',
            note: 'Une app, deux marques',
        },
        auberi: {
            detail: {
                role: 'Maison cliente de Jewely. Site e-commerce sur la plateforme commune.',
            },
            label: 'Auberi',
            note: 'Maison cliente de Jewely',
        },
        cases: {
            detail: { role: 'Des cas écrits, une décision à la fois.' },
            label: 'Études de cas',
            note: 'Cas écrits',
        },
        catalogs: {
            detail: {
                role: 'Création automatique de produits et synchronisation vers Google Merchant Center et Facebook Catalog.',
            },
            label: 'Catalogues\nGoogle et Meta',
            note: 'Flux produit',
        },
        cms: {
            detail: {
                period: '2026',
                role: 'Un e-commerce simple transformé en CMS sur mesure, avec un back end commun, déployé pour plusieurs maisons clientes.',
            },
            label: 'CMS Jewely',
            note: 'Un back end, plusieurs sites',
        },
        cmsCore: {
            detail: {
                period: '2026',
                role: 'Un back end commun à plusieurs sites clients : une fonctionnalité se construit une fois et arrive sur tous les sites.',
            },
            label: 'Back end Laravel\ncommun',
            note: 'Construit une fois, sur tous les sites',
        },
        consent: {
            detail: {
                role: 'Consent Mode v2, gestionnaire de balises et data layer, Meta Pixel, Bing UET, Adobe Analytics : une mesure qui attend le visiteur.',
            },
            label: 'Consentement\net mesure',
            note: 'Consent Mode v2, data layer',
        },
        contact: {
            detail: { role: 'Nancy, Paris et à distance.' },
            label: 'Contact',
            note: 'Nancy, Paris, à distance',
        },
        crown: {
            detail: {
                period: '2025',
                role: 'Montres de luxe à Strasbourg. Réalisé seul, de A à Z : j’ai intégré la maquette fournie par une agence de Strasbourg, et tout le monde a été ravi du résultat.',
            },
            label: 'Crown-DP',
            note: 'Site e-commerce sur mesure',
        },
        docker: {
            detail: {
                period: '2024–2026',
                role: 'Déploiements automatisés via EventBridge, Lambda et SSM, puis un pipeline qui dit ce qui s’est vraiment passé, après un incident disque.',
            },
            label: 'Docker Swarm\nsur AWS',
            note: 'Des déploiements qui disent vrai',
        },
        erpPim: {
            detail: {
                role: 'Des connecteurs entre l’ERP, le PIM et les catalogues marchands : stocks, enrichissement, création automatique de produits.',
            },
            label: 'Flux ERP\net PIM',
            note: 'La donnée produit, de bout en bout',
        },
        experience: {
            detail: { role: 'Les postes, un par un, avec la stack de chacun.' },
            label: 'Expériences',
            note: 'Postes et stacks',
        },
        florian: {
            detail: {
                period: '2026',
                role: 'Portfolio de Florian Rosinski, artiste et chercheur en communication. Développement.',
            },
            label: 'florianrosinski.fr',
            note: 'Portfolio, que j’ai développé',
        },
        godechot: {
            detail: {
                role: 'Maison cliente de Jewely. J’ai réalisé son espace Rolex Certified Pre-Owned.',
            },
            label: 'Godechot-Pauliet',
            note: 'Maison cliente de Jewely',
        },
        jewely: {
            detail: {
                figures: [
                    { label: 'ans, de 2021 à 2026', value: '5' },
                    { label: 'fiches produit enrichies', value: '20 000+' },
                ],
                links: [{ href: '/experience', label: 'Lire l’expérience' }],
                period: '2021–2026',
                role: 'Développeur e-commerce, de l’alternance à la plateforme commune de plusieurs maisons de bijouterie-horlogerie. 2026 : un CMS sur mesure à back end commun.',
            },
            label: 'Jewely / Flippad',
            note: 'ERP, PIM et e-commerce pour l’horlogerie-bijouterie',
        },
        journal: {
            detail: { role: 'Notes et articles issus du travail.' },
            label: 'Journal',
            note: 'Notes de travail',
        },
        julian: {
            detail: {
                role: 'Maison cliente de Jewely. J’ai réalisé son espace Rolex.',
            },
            label: 'Louis Julian',
            note: 'Maison cliente de Jewely',
        },
        nextts: {
            detail: {
                role: 'Sites et outils en TypeScript : astralmanach, Un art voulu voyant, florianrosinski.fr.',
            },
            label: 'Next.js et\nTypeScript',
            note: 'Sites et outils récents',
        },
        plm: {
            detail: {
                period: '2018–2019',
                role: 'Projets de mobilité et information sur la citoyenneté européenne auprès de jeunes.',
            },
            label: 'Parcours le Monde',
            note: 'Chargé d’accompagnement, 2018–2019',
        },
        python: {
            detail: {
                figures: [{ label: 'fiches produit', value: '20 000+' }],
                role: 'Des algorithmes de scrap et d’enrichissement sur le PIM maison, pour les produits, les images et les vidéos.',
            },
            label: 'Scrap et\nenrichissement Python',
            note: 'Sur le PIM maison',
        },
        rolexBespoke: {
            detail: {
                role: 'Toutes les demandes passaient par moi. Sous NDA avec Rolex.',
            },
            label: 'Rolex Bespoke',
            note: 'Dispositif Rolex',
        },
        rolexCpo: {
            label: 'Rolex Certified\nPre-Owned',
            note: 'Espace Rolex de Godechot-Pauliet',
        },
        rolexJulian: {
            label: 'Espace Rolex',
            note: 'Espace Rolex de Louis Julian',
        },
        sensitive: {
            detail: {
                role: 'Un outil auto-hébergé pour des équipes de terrain, pensé pour un usage simple, des moyens limités et des données sensibles.',
            },
            label: 'Données sensibles,\nauto-hébergement',
            note: 'Données de santé, moyens limités',
        },
        seo: {
            detail: {
                role: 'URLs, listes, fiches produit, sitemaps et données structurées, pensés pendant les refontes.',
            },
            label: 'SEO technique\net JSON-LD',
            note: 'URLs, sitemaps, données structurées',
        },
        services: {
            detail: {
                role: 'Les stacks que je maîtrise et des tarifs de départ.',
            },
            label: 'Services',
            note: 'Stacks et tarifs de départ',
        },
        uavv: {
            detail: {
                period: '2026',
                role: 'Site d’un projet artistique de Florian Rosinski. Développement et intégration, sur sa direction artistique.',
            },
            label: 'Un art voulu voyant',
            note: 'Site d’un projet artistique, que j’ai développé',
        },
        work: {
            detail: { role: 'Les projets, un lot chacun.' },
            label: 'Réalisations',
            note: 'Quatre lots, un par projet',
        },
    },
    kickers: {
        article: 'Article',
        case: 'Étude de cas',
        external: 'Lien externe',
        house: 'Maison cliente',
        note: 'Note',
        notion: 'Technologie',
        page: 'Page du site',
        project: 'Projet ou poste',
    },
    legend: {
        article: 'Article',
        case: 'Étude de cas',
        external: 'Lien externe',
        hint: 'Survolez une case, ou touchez-la.',
        note: 'Note',
        notion: 'Technologie ou notion',
        page: 'Page du site',
    },
    listTitle: 'La même chose, en liste',
    notionsLabel: 'Avec',
    readLabel: 'À lire',
    revision: 'Ind. C · 2026-10-01',
    title: 'Projets, postes et ce qu’ils m’ont appris',
} satisfies Reference;
