import type { PublicLocale } from '@/types';

/**
 * UI copy for the contact page. See `copy/home.ts` for why this is a record
 * keyed by locale rather than an inline ternary.
 *
 * Values that simply forward a prop (the location label, the location itself)
 * are deliberately absent: they are not translated copy and are read straight
 * from props at the point of use.
 */
export type ContactCopy = {
    emailHeroCta: string;
    whatsappLabel: string;
    availabilityLabel: string;
    availabilityText: string;
    opportunitiesLabel: string;
    opportunitiesText: string;
    /** Prefix for the generated mailto: subject line. */
    subjectPrefix: string;
    bodyNameLabel: string;
    bodyEmailLabel: string;
    bodyCompanyLabel: string;
    bodyBriefFallback: string;
    recruiterFitLabel: string;
    recruiterFitRoles: string[];
    recruiterDecisionLabel: string;
    recruiterDecisionCopy: string;
    cvLabel: string;
    portraitAlt: string;
    staticPreviewTitle: string;
    staticPreviewSummary: string;
};

export const contactCopy: Record<PublicLocale, ContactCopy> = {
    fr: {
        emailHeroCta: 'Écrire un mail',
        whatsappLabel: 'WhatsApp',
        availabilityLabel: 'Disponibilité',
        availabilityText: '● Freelance / temps partiel / échanges',
        opportunitiesLabel: 'Situation actuelle',
        opportunitiesText:
            "Ouvert aux opportunités tech-lead / engineering, missions freelance à temps partiel et échanges autour d'un produit en route — recrutement, renfort technique ou cadrage sur un sujet précis.",
        subjectPrefix: 'Prise de contact',
        bodyNameLabel: 'Nom',
        bodyEmailLabel: 'Email',
        bodyCompanyLabel: 'Entreprise ou produit',
        bodyBriefFallback: 'Brief projet :',
        recruiterFitLabel: 'Rôles et terrains',
        recruiterFitRoles: [
            'Lead web / produit',
            'Gestion de projet',
            'Développeur full stack',
            'E-commerce et CMS',
            'Connecteurs et data',
            'Tracking / consentement',
            'Outils internes',
        ],
        recruiterDecisionLabel: 'Formats',
        recruiterDecisionCopy:
            "Recrutement, renfort ponctuel, mission freelance, revue technique, reprise de plateforme, connecteur à fiabiliser ou premier échange autour d'un produit déjà lancé.",
        cvLabel: 'CV',
        portraitAlt: "Portrait illustré d'Ismael Rodmacq",
        staticPreviewTitle: 'Preview statique',
        staticPreviewSummary:
            "Le formulaire est volontairement retiré de ce package HTML. Pour échanger, le plus direct reste l'email.",
    },
    en: {
        emailHeroCta: 'Write an email',
        whatsappLabel: 'WhatsApp',
        availabilityLabel: 'Availability',
        availabilityText: '● Freelance / part-time / conversations',
        opportunitiesLabel: 'Current situation',
        opportunitiesText:
            'Open to tech-lead / engineering opportunities, part-time freelance work, and conversations around a product already in motion — hiring, technical reinforcement, or scoping on a focused topic.',
        subjectPrefix: 'Inquiry',
        bodyNameLabel: 'Name',
        bodyEmailLabel: 'Email',
        bodyCompanyLabel: 'Company or product',
        bodyBriefFallback: 'Project brief:',
        recruiterFitLabel: 'Roles and contexts',
        recruiterFitRoles: [
            'Web / product lead',
            'Project delivery',
            'Full-stack developer',
            'E-commerce and CMS',
            'Connectors and data',
            'Tracking / consent',
            'Internal tools',
        ],
        recruiterDecisionLabel: 'Formats',
        recruiterDecisionCopy:
            'Hiring, freelance support, technical review, platform recovery, connector hardening, or a first conversation around a product already in motion.',
        cvLabel: 'CV',
        portraitAlt: 'Illustrated portrait of Ismael Rodmacq',
        staticPreviewTitle: 'Static preview',
        staticPreviewSummary:
            'The contact form is intentionally removed from this HTML package. Email remains the direct path.',
    },
};
