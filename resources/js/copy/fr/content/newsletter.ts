/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/content/newsletter').default;

export default {
    contexts: {
        'case-study': {
            eyebrow: 'Newsletter',
            summary:
                'Un email quand une nouvelle étude de cas ou un playbook growth concret est publié. Aucun bruit entre deux.',
            title: 'Lire le prochain chantier avant vos concurrents.',
        },
        home: {
            eyebrow: 'Newsletter',
            summary:
                'Des notes concrètes et occasionnelles sur ce qui fait vendre un site local : vitesse, donnée produit, campagnes qui respectent le consentement.',
            title: 'Des conseils web pour les commerces qui avancent.',
        },
        journal: {
            eyebrow: 'Newsletter',
            summary:
                'Les nouveaux billets du journal et les write-ups techniques, du dépôt à votre boîte mail. Rien d’autre.',
            title: 'Recevoir le prochain billet par email.',
        },
    },
    emailLabel: 'Adresse email',
    emailPlaceholder: 'vous@exemple.fr',
    errorNote: 'L’envoi n’a pas abouti. Réessayez dans une minute.',
    pendingNote:
        'Encore une étape — un email de confirmation arrive. L’inscription ne démarre qu’après votre clic.',
    privacyNote:
        'Double opt-in. Désinscription en un clic. Aucun pixel de tracking.',
    submitCta: 'S’abonner',
    submittingCta: 'Envoi…',
} satisfies Reference;
