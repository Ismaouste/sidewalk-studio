/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/layout/footer').default;

export default {
    backToTopLabel: 'Retour en haut',
    colophonLabel: 'Colophon',
    consentNote: 'Mesure d’audience en opt-in explicite.',
    contactLabel: 'Mail',
    dataLabel: 'Traitement des données',
    linkedinLabel: 'LinkedIn',
    staticPreviewNote:
        'Preview statique : formulaire et préférences avancées désactivés.',
} satisfies Reference;
