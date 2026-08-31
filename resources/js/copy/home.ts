import type { PublicLocale } from '@/types';

/**
 * UI copy for the home page.
 *
 * Keyed by locale as `Record<PublicLocale, T>` rather than written as an
 * inline `locale === 'fr' ? {…} : {…}` ternary. A ternary between two object
 * literals infers a union, so a key added to only one branch becomes optional
 * and renders as empty text in the other language with no error. A record of a
 * named type makes that a compile error instead.
 */
export type HeroCapabilityCopy = {
    label: string;
    tone: 'dominant' | 'green' | 'sun' | 'coral' | 'violet';
    /** Shown on the accent chips under the hero lead. */
    details: string;
    /** Shorter form used inside the current-role panel. */
    panelDetails: string;
};

export const heroCapabilityCopy: Record<PublicLocale, HeroCapabilityCopy[]> = {
    fr: [
        {
            label: 'Sites marchands',
            tone: 'violet',
            details:
                'WooCommerce / PrestaShop / Shopify / Alokai (ex Vue Storefront)',
            panelDetails: 'WooCommerce / PrestaShop / Shopify / Alokai',
        },
        {
            label: 'Laravel',
            tone: 'green',
            details: 'Laravel / PHP / APIs / CI-CD',
            panelDetails: 'Laravel / PHP / APIs / CI-CD',
        },
        {
            label: 'Data produit et SEO',
            tone: 'sun',
            details: 'PIM / JSON-LD / Merchant Center / Data layer',
            panelDetails: 'PIM / JSON-LD / Merchant Center / Data layer',
        },
    ],
    en: [
        {
            label: 'E-commerce',
            tone: 'violet',
            details:
                'WooCommerce / PrestaShop / Shopify / Alokai (formerly Vue Storefront)',
            panelDetails: 'WooCommerce / PrestaShop / Shopify / Alokai',
        },
        {
            label: 'Laravel',
            tone: 'green',
            details: 'Laravel / PHP / APIs / CI/CD',
            panelDetails: 'Laravel / PHP / APIs / CI/CD',
        },
        {
            label: 'Product data and SEO',
            tone: 'sun',
            details: 'PIM / JSON-LD / Merchant Center / Data layer',
            panelDetails: 'PIM / JSON-LD / Merchant Center / Data layer',
        },
    ],
};

export type HomeCopy = {
    projectsCta: string;
    contactCta: string;
    currentFrameLabel: string;
    heroPanelTitle: string;
    heroPanelSummarySuffix: string;
    hbjoatDefinition: string;
    cmsDefinition: string;
    phpDefinition: string;
    apiDefinition: string;
    ciCdDefinition: string;
    seoDefinition: string;
    pimDefinition: string;
    jsonLdDefinition: string;
    merchantCenterDefinition: string;
    dataLayerDefinition: string;
    whatIDoLabel: string;
    focusTitle: string;
    focusDescription: string;
    selectedWorkLabel: string;
    projectsTitle: string;
    projectsDescription: string;
    openProjectsCta: string;
    internalBuildLabel: string;
    notesLabel: string;
    contactLabel: string;
    startConversationCta: string;
    referencesCta: string;
    archiveCta: string;
};

export const homeCopy: Record<PublicLocale, HomeCopy> = {
    fr: {
        projectsCta: 'Lire les expériences',
        contactCta: 'Prendre contact',
        currentFrameLabel: "Aujourd'hui",
        heroPanelTitle: 'Développeur e-commerce chez Jewely / Flippad',
        heroPanelSummarySuffix:
            'écosystème HBJO, ERP, PIM, flux produit, tracking et SEO technique.',
        hbjoatDefinition: 'Horlogerie, bijouterie, joaillerie et orfèvrerie.',
        cmsDefinition:
            'Content Management System : système de gestion de contenu.',
        phpDefinition:
            'PHP : langage serveur largement utilisé pour les applications web et e-commerce.',
        apiDefinition:
            "API : interface d'échange entre services, outils métier et applications.",
        ciCdDefinition:
            'CI/CD : intégration et déploiement continus pour fiabiliser les mises en ligne.',
        seoDefinition:
            'SEO : optimisation technique et éditoriale pour rendre un site lisible par les moteurs et utile aux visiteurs.',
        pimDefinition:
            'PIM : Product Information Management, le socle qui centralise et structure la donnée produit.',
        jsonLdDefinition:
            'JSON-LD : format de données structurées lisible par les moteurs et les plateformes.',
        merchantCenterDefinition:
            'Google Merchant Center : flux catalogue et diffusion produit vers les surfaces shopping Google.',
        dataLayerDefinition:
            'Data layer : couche de données partagée entre le site, le tracking et les outils marketing.',
        whatIDoLabel: 'Ce que je fais',
        focusTitle: 'Un positionnement net dans des environnements complexes.',
        focusDescription:
            'Le travail se situe souvent entre livraison produit, modernisation du legacy, SEO technique, vie privée et besoin de garder des systèmes compréhensibles après mise en production.',
        selectedWorkLabel: 'Expérience',
        projectsTitle: 'Études de cas et repères à ouvrir ensuite.',
        projectsDescription:
            'Études de cas, notes et références pour entrer dans des situations plus concrètes.',
        openProjectsCta: 'Découvrir les projets',
        internalBuildLabel: 'Interne',
        notesLabel: 'Notes',
        contactLabel: 'Contact',
        startConversationCta: 'Prendre contact',
        referencesCta: 'Lire les expériences',
        archiveCta: 'Découvrir toutes les études de cas',
    },
    en: {
        projectsCta: 'View experiences',
        contactCta: 'Start a conversation',
        currentFrameLabel: 'Current role',
        heroPanelTitle: 'E-commerce developer at Jewely / Flippad',
        heroPanelSummarySuffix:
            'HBJO commerce, ERP, PIM, product flows, tracking, and technical SEO.',
        hbjoatDefinition: 'Watchmaking, jewelry, silverware, and tableware.',
        cmsDefinition: 'CMS: Content Management System.',
        phpDefinition:
            'PHP: a server-side language widely used for web and e-commerce applications.',
        apiDefinition:
            'API: an interface used to connect services, business tools, and applications.',
        ciCdDefinition:
            'CI/CD: continuous integration and delivery practices that make releases safer.',
        seoDefinition:
            'SEO: technical and editorial optimization that helps a site stay legible for search engines and useful for people.',
        pimDefinition:
            'PIM: Product Information Management, the layer that centralizes and structures product data.',
        jsonLdDefinition:
            'JSON-LD: a structured-data format understood by search engines and platforms.',
        merchantCenterDefinition:
            'Google Merchant Center: product feed distribution across Google shopping surfaces.',
        dataLayerDefinition:
            'Data layer: the shared data layer used by the site, tracking, and marketing tools.',
        whatIDoLabel: 'What I do',
        focusTitle: 'A legible practice for complex environments.',
        focusDescription:
            'The work usually sits between product delivery, legacy modernization, technical SEO, privacy, and the need to keep systems readable after launch.',
        selectedWorkLabel: 'Experience',
        projectsTitle: 'Case studies and pointers worth opening next.',
        projectsDescription:
            'Case studies, notes, and references that open more concrete implementation contexts.',
        openProjectsCta: 'Open case studies',
        internalBuildLabel: 'Internal build',
        notesLabel: 'Notes',
        contactLabel: 'Contact',
        startConversationCta: 'Start a conversation',
        referencesCta: 'View experiences',
        archiveCta: 'Browse all case studies',
    },
};
