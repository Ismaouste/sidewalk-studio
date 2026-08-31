/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/contact').default;

export default {
    availabilityLabel: 'Disponibilité',
    availabilityText: '● Freelance / temps partiel / échanges',
    bodyBriefFallback: 'Brief projet :',
    bodyCompanyLabel: 'Entreprise ou produit',
    bodyEmailLabel: 'Email',
    bodyNameLabel: 'Nom',
    cvLabel: 'CV',
    emailHeroCta: 'Écrire un mail',
    opportunitiesLabel: 'Situation actuelle',
    opportunitiesText:
        "Ouvert aux opportunités tech-lead / engineering, missions freelance à temps partiel et échanges autour d'un produit en route — recrutement, renfort technique ou cadrage sur un sujet précis.",
    portraitAlt: "Portrait illustré d'Ismael Rodmacq",
    recruiterDecisionCopy:
        "Recrutement, renfort ponctuel, mission freelance, revue technique, reprise de plateforme, connecteur à fiabiliser ou premier échange autour d'un produit déjà lancé.",
    recruiterDecisionLabel: 'Formats',
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
    staticPreviewSummary:
        "Le formulaire est volontairement retiré de ce package HTML. Pour échanger, le plus direct reste l'email.",
    staticPreviewTitle: 'Preview statique',
    subjectPrefix: 'Prise de contact',
    whatsappLabel: 'WhatsApp',
} satisfies Reference;
