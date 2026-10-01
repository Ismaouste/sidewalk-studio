/**
 * The timeline on the home page: projects and roles on a time axis, the
 * technologies and notions, the pages of the site and the publications. Dates
 * come from the CV and the experience page; publications are read from the
 * journal and the case studies, never written here.
 *
 * English copy. This is the reference shape the French file is checked
 * against. Keys are sorted; `sort-keys` enforces it in lint.
 */
export default {
    caption:
        'Projects and roles on a time axis, what I used, and where it is told. Point at a box, or tap it, for the details.',
    columns: {
        notions: 'Technologies and notions',
        pages: 'Pages and writing',
        projects: 'Projects and roles',
    },
    items: {
        aremedia: {
            detail: {
                period: '2020–2021',
                role: 'A self-hosted reporting tool for field teams in public health, and the rebuild of the public site.',
            },
            label: 'Aremedia',
            note: 'Public health: a self-hosted field tool and the public site',
        },
        artem: {
            detail: {
                period: '2016–2017',
                role: 'Mediation between institutions and partner organizations. A sustainability awareness day for 170 students and 15 partners.',
            },
            label: 'Alliance Artem',
            note: 'Mediator, 2016–2017',
        },
        astralmanach: {
            detail: {
                figures: [
                    { label: 'sources', value: '34' },
                    { label: 'tools', value: '3' },
                ],
                links: [
                    {
                        href: 'https://www.npmjs.com/package/astralmanach',
                        label: 'npm',
                    },
                    {
                        href: 'https://astralmanach.eu/api-publique',
                        label: 'Public API',
                    },
                ],
                period: '2026',
                role: 'The sky of a date, a time and a place, as data: an npm library under MIT, a public read-only API and three tools.',
            },
            label: 'astralmanach',
            note: 'The sky of a date, as data',
        },
        astro: {
            detail: {
                role: 'The Moon, the planets, eclipses and the space station for a date, a time and a place, computed rather than looked up.',
            },
            label: 'Astronomy\ncalculations',
            note: 'The sky as data',
        },
        atlas: {
            detail: {
                period: '2026',
                role: 'One app, two brands that do not work with tradespeople the same way: as subcontractors or as partners. Design and development.',
            },
            label: 'DD le Dépanneur,\nAtlas Dépannage',
            note: 'One app, two brands',
        },
        auberi: {
            detail: {
                role: 'Jewely client house. E-commerce site on the shared platform.',
            },
            label: 'Auberi',
            note: 'Jewely client house',
        },
        cases: {
            detail: { role: 'Written cases, one decision at a time.' },
            label: 'Case studies',
            note: 'Written cases',
        },
        catalogs: {
            detail: {
                role: 'Automatic product creation and synchronization toward Google Merchant Center and Facebook Catalog.',
            },
            label: 'Google and Meta\ncatalogs',
            note: 'Product feeds',
        },
        cms: {
            detail: {
                period: '2026',
                role: 'A simple e-commerce site turned into a custom CMS with one common back end, deployed for several client houses.',
            },
            label: 'Jewely CMS',
            note: 'One back end, several sites',
        },
        cmsCore: {
            detail: {
                period: '2026',
                role: 'One common back end for several client sites: a feature is built once and reaches every site.',
            },
            label: 'Shared Laravel\nback end',
            note: 'Built once, on every site',
        },
        consent: {
            detail: {
                role: 'Consent Mode v2, tag manager and data layer, Meta Pixel, Bing UET, Adobe Analytics: measurement that waits for the visitor.',
            },
            label: 'Consent and\nmeasurement',
            note: 'Consent Mode v2, data layer',
        },
        contact: {
            detail: { role: 'Nancy, Paris and remote.' },
            label: 'Contact',
            note: 'Nancy, Paris, remote',
        },
        crown: {
            detail: {
                period: '2025',
                role: 'Luxury watches, Strasbourg. Built alone, from start to finish: I integrated the design supplied by a Strasbourg agency, and everyone was happy with the result.',
            },
            label: 'Crown-DP',
            note: 'Custom e-commerce site',
        },
        docker: {
            detail: {
                period: '2024–2026',
                role: 'Automated deploys through EventBridge, Lambda and SSM, then a pipeline that reports what really happened, after a disk incident.',
            },
            label: 'Docker Swarm\non AWS',
            note: 'Deploys that tell the truth',
        },
        erpPim: {
            detail: {
                role: 'Connectors between the ERP, the PIM and merchant catalogs: stock, enrichment, automatic product creation.',
            },
            label: 'ERP and\nPIM flows',
            note: 'Product data, end to end',
        },
        experience: {
            detail: { role: 'The roles, one by one, with the stack of each.' },
            label: 'Experience',
            note: 'Roles and stacks',
        },
        florian: {
            detail: {
                period: '2026',
                role: 'Portfolio of Florian Rosinski, artist and researcher in communication. Development.',
            },
            label: 'florianrosinski.fr',
            note: 'Portfolio, which I developed',
        },
        godechot: {
            detail: {
                role: 'Jewely client house. I built its Rolex Certified Pre-Owned space.',
            },
            label: 'Godechot-Pauliet',
            note: 'Jewely client house',
        },
        jewely: {
            detail: {
                figures: [
                    { label: 'years, 2021 to 2026', value: '5' },
                    { label: 'product entries enriched', value: '20,000+' },
                ],
                links: [{ href: '/experience', label: 'Read the experience' }],
                period: '2021–2026',
                role: 'E-commerce developer, from apprentice to the shared platform of several jewelry and watch houses. 2026: a custom CMS with one common back end.',
            },
            label: 'Jewely / Flippad',
            note: 'ERP, PIM and e-commerce for watchmaking and jewellery',
        },
        journal: {
            detail: { role: 'Notes and articles from the work.' },
            label: 'Journal',
            note: 'Working notes',
        },
        julian: {
            detail: {
                role: 'Jewely client house. I built its Rolex space.',
            },
            label: 'Louis Julian',
            note: 'Jewely client house',
        },
        nextts: {
            detail: {
                role: 'Sites and tools in TypeScript: astralmanach, Un art voulu voyant, florianrosinski.fr.',
            },
            label: 'Next.js and\nTypeScript',
            note: 'Recent sites and tools',
        },
        plm: {
            detail: {
                period: '2018–2019',
                role: 'Mobility projects and guidance on European citizenship for young people.',
            },
            label: 'Parcours le Monde',
            note: 'Support officer, 2018–2019',
        },
        python: {
            detail: {
                figures: [{ label: 'product entries', value: '20,000+' }],
                role: 'Scraping and enrichment algorithms on the in-house PIM, for products, images and videos.',
            },
            label: 'Python scraping\nand enrichment',
            note: 'On the in-house PIM',
        },
        rolexBespoke: {
            detail: {
                role: 'Every request went through me. Under NDA with Rolex.',
            },
            label: 'Rolex Bespoke',
            note: 'Rolex programme',
        },
        rolexCpo: {
            label: 'Rolex Certified\nPre-Owned',
            note: 'Rolex space of Godechot-Pauliet',
        },
        rolexJulian: {
            label: 'Rolex space',
            note: 'Rolex space of Louis Julian',
        },
        sensitive: {
            detail: {
                role: 'A self-hosted tool for field teams, built for simple use, limited means and sensitive data.',
            },
            label: 'Sensitive data,\nself-hosted',
            note: 'Health data, limited means',
        },
        seo: {
            detail: {
                role: 'URLs, listings, product pages, sitemaps and structured data, shaped during redesigns.',
            },
            label: 'Technical SEO\nand JSON-LD',
            note: 'URLs, sitemaps, structured data',
        },
        services: {
            detail: { role: 'The stacks I work with and starting prices.' },
            label: 'Services',
            note: 'Stacks and starting prices',
        },
        uavv: {
            detail: {
                period: '2026',
                role: 'Website of an art project by Florian Rosinski. Development and integration, on his artistic direction.',
            },
            label: 'Un art voulu voyant',
            note: 'Art project website, which I developed',
        },
        work: {
            detail: { role: 'The projects, one lot each.' },
            label: 'Work',
            note: 'Four lots, one per project',
        },
    },
    kickers: {
        article: 'Article',
        case: 'Case study',
        external: 'External link',
        house: 'Client house',
        note: 'Note',
        notion: 'Technology',
        page: 'Page of the site',
        project: 'Project or role',
    },
    legend: {
        article: 'Article',
        case: 'Case study',
        external: 'External link',
        hint: 'Point at a box, or tap it.',
        note: 'Note',
        notion: 'Technology or notion',
        page: 'Page of the site',
    },
    listTitle: 'The same, as a list',
    notionsLabel: 'With',
    readLabel: 'Read',
    revision: 'Rev. C · 2026-10-01',
    title: 'Projects, roles and what they taught me',
};
