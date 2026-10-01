/**
 * English copy. This is the reference shape the French file is checked
 * against. Keys are sorted; `sort-keys` enforces it in lint.
 */
export default {
    apiDefinition:
        'API: an interface used to connect services, business tools, and applications.',
    archiveCta: 'Browse all case studies',
    ciCdDefinition:
        'CI/CD: continuous integration and delivery practices that make releases safer.',
    clients: {
        figures: [
            { label: 'years at Jewely / Flippad', suffix: '', value: 5 },
            { label: 'product entries enriched', suffix: '+', value: 20000 },
            { label: 'client houses', suffix: '', value: 4 },
        ],
        houses: [
            {
                href: 'https://www.crown-dp.com/fr',
                linkLabel: 'crown-dp.com',
                name: 'Crown-DP',
                period: '2025',
                role: 'Luxury watches, Strasbourg. Built alone, from start to finish: I integrated the design supplied by a Strasbourg agency, and everyone was happy with the result.',
            },
            {
                href: 'https://www.godechot-pauliet.com/en/rolex-certified-pre-owned/',
                linkLabel: 'Rolex Certified Pre-Owned space',
                name: 'Godechot-Pauliet',
                period: '',
                role: 'I built its Rolex Certified Pre-Owned space.',
            },
            {
                href: '',
                linkLabel: '',
                name: 'Louis Julian',
                period: '',
                role: 'I built its Rolex space.',
            },
            {
                href: '',
                linkLabel: '',
                name: 'Auberi',
                period: '',
                role: 'E-commerce site on the shared platform.',
            },
        ],
        intro: 'Four houses of the Jewely client portfolio, built on one shared back end. Under NDA with Rolex; the Rolex Bespoke requests also went through me.',
        title: 'Built for the Jewely client houses',
    },
    cmsDefinition: 'CMS: Content Management System.',
    contactCta: 'Start a conversation',
    contactLabel: 'Contact',
    currentFrameLabel: 'Last role',
    dataLayerDefinition:
        'Data layer: the shared data layer used by the site, tracking, and marketing tools.',
    focusDescription:
        'Product delivery, modernisation of existing systems, technical SEO and privacy, and keeping systems maintainable after launch.',
    focusTitle: 'What I work on',
    hbjoatDefinition: 'Watchmaking, jewelry, silverware, and tableware.',
    hbjoatLabel: 'HBJOAT',
    heroCapabilities: [
        {
            details:
                'WooCommerce / PrestaShop / Shopify / Alokai (formerly Vue Storefront)',
            label: 'E-commerce',
            panelDetails: 'WooCommerce / PrestaShop / Shopify / Alokai',
            tone: 'violet' as const,
        },
        {
            details: 'Laravel / PHP / APIs / CI/CD',
            label: 'Laravel',
            panelDetails: 'Laravel / PHP / APIs / CI/CD',
            tone: 'green' as const,
        },
        {
            details: 'PIM / JSON-LD / Merchant Center / Data layer',
            label: 'Product data and SEO',
            panelDetails: 'PIM / JSON-LD / Merchant Center / Data layer',
            tone: 'sun' as const,
        },
    ],
    heroPanelSummarySuffix:
        'HBJO commerce, ERP, PIM, product flows, tracking, and technical SEO.',
    heroPanelTitle: 'E-commerce developer, Jewely / Flippad (2021–2026)',
    internalBuildLabel: 'Internal build',
    jsonLdDefinition:
        'JSON-LD: a structured-data format understood by search engines and platforms.',
    laravelDefinition: 'PHP framework for modern web applications.',
    merchantCenterDefinition:
        'Google Merchant Center: product feed distribution across Google shopping surfaces.',
    notesLabel: 'Notes',
    openProjectsCta: 'Open case studies',
    phpDefinition:
        'PHP: a server-side language widely used for web and e-commerce applications.',
    pimDefinition:
        'PIM: Product Information Management, the layer that centralizes and structures product data.',
    plan: {
        caption: 'The pages of the site, the projects and what each holds.',
        edges: {
            design: 'design',
            engine: 'engine',
        },
        legend: [
            'Red: you are here',
            'Ink: the pages of the site',
            'Blue: astralmanach and its parts',
            'A line between two lots: a link or an interface',
        ],
        lots: {
            api: { label: 'Public API', note: 'OpenAPI, Postman, 34 sources' },
            app: {
                label: 'DD le Dépanneur\nAtlas Dépannage',
                note: 'One app, two brands',
            },
            aremedia: {
                label: 'Aremedia',
                note: 'Public health and popular education, Paris: a self-hosted field tool and the public site',
            },
            astralmanach: {
                label: 'astralmanach',
                note: 'The sky of a date, as data',
            },
            caseConsent: {
                label: 'Consent and measurement',
                note: 'Case study: consent before measurement',
            },
            caseDeploy: {
                label: 'E-commerce deployment',
                note: 'Case study: a deployment pipeline',
            },
            caseFlux: {
                label: 'ERP and PIM flows',
                note: 'Case study: product data between ERP, PIM and shop',
            },
            cases: { label: 'Case studies', note: 'Four written cases' },
            caseTools: {
                label: 'Non-profit tool',
                note: 'Case study: self-hosting and sensitive data',
            },
            clientAuberi: {
                label: 'Auberi',
                note: 'Jewely client house, e-commerce site',
            },
            clientCrown: {
                detail: {
                    links: [
                        {
                            href: 'https://www.crown-dp.com/fr',
                            label: 'crown-dp.com',
                            nofollow: true,
                        },
                    ],
                    period: '2025',
                    role: 'Built alone, from start to finish. I integrated the design supplied by a Strasbourg agency.',
                },
                label: 'Crown-DP',
                note: 'Luxury watches, Strasbourg: custom e-commerce site',
            },
            clientGodechot: {
                detail: {
                    links: [
                        {
                            href: 'https://www.godechot-pauliet.com/en/rolex-certified-pre-owned/',
                            label: 'Rolex Certified Pre-Owned space',
                            nofollow: true,
                        },
                    ],
                    role: 'Jewely client house. I built its Rolex Certified Pre-Owned space.',
                },
                label: 'Godechot-Pauliet',
                note: 'Jewely client house, e-commerce site',
            },
            clientJulian: {
                detail: {
                    role: 'Jewely client house. I built its Rolex space.',
                },
                label: 'Louis Julian',
                note: 'Jewely client house, e-commerce site',
            },
            contact: { label: 'Contact', note: 'Nancy, Paris, remote' },
            experience: {
                label: 'Experience',
                note: 'Jewely, Aremedia, Parcours le Monde',
            },
            florian: {
                label: 'florianrosinski.fr',
                note: 'Portfolio of Florian Rosinski, which I developed',
            },
            home: { label: 'Home', note: 'You are here' },
            jewely: {
                detail: {
                    figures: [
                        { label: 'years, 2021 to 2026', value: '5' },
                        { label: 'product entries enriched', value: '20,000+' },
                    ],
                    links: [
                        { href: '/experience', label: 'Read the experience' },
                    ],
                    role: 'E-commerce developer. 2026: a custom CMS with one common back end for several client houses.',
                },
                label: 'Jewely / Flippad',
                note: 'ERP, PIM and e-commerce for watchmaking and jewellery',
            },
            journal: { label: 'Journal', note: 'Working notes' },
            labs: {
                label: 'Labs',
                note: 'Core Web Vitals audit and sandboxes',
            },
            lib: { label: 'npm library', note: 'MIT licence, ESM and types' },
            obs: {
                label: 'Observations',
                note: 'A printable plate of the sky of a date',
            },
            plm: {
                label: 'Parcours le Monde',
                note: 'International mobility for young people, still active in Marseille',
            },
            rolexBespoke: {
                detail: {
                    role: 'Every request went through me. Under NDA with Rolex.',
                },
                label: 'Rolex Bespoke',
                note: 'Rolex programme: I handle every request',
            },
            rolexCpo: {
                label: 'Rolex Certified\nPre-Owned',
                note: 'Rolex space of Godechot-Pauliet',
            },
            rolexJulian: {
                label: 'Rolex space',
                note: 'Rolex space of Louis Julian',
            },
            services: { label: 'Services', note: 'Stacks and starting prices' },
            tools: {
                label: 'Theme, Sky,\nCertificate',
                note: 'Three public tools',
            },
            uavv: {
                label: 'Un art voulu voyant',
                note: 'Website of an art project by Florian Rosinski, which I developed',
            },
            work: { label: 'Work', note: 'Four lots, one per project' },
        },
        revision: 'Rev. B · 2026-10-01',
        scale: '1 block = 1 lot',
        title: 'Plan of the work',
    },
    projectsCta: 'View experiences',
    projectsDescription: 'Written cases and notes on concrete situations.',
    projectsTitle: 'Case studies and notes',
    referencesCta: 'View experiences',
    selectedWorkLabel: 'Experience',
    seoDefinition:
        'SEO: technical and editorial optimization that helps a site stay legible for search engines and useful for people.',
    startConversationCta: 'Start a conversation',
    whatIDoLabel: 'What I do',
};
