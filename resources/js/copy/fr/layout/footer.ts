/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/layout/footer').default;

export default {
    backToTopLabel: 'Retour en haut',
    cartouche: {
        author: 'Auteur',
        find: 'Me trouver',
        place: 'Lieu',
        project: 'Projet',
        revision: 'Ind.',
        revisionValue: 'A · 2026-09-30',
        scale: 'Échelle',
        sheet: 'Planche',
    },
    colophonLabel: 'Colophon',
    dataLabel: 'Traitement des données',
    licenseLabel: '© MIT',
    linkedinLabel: 'LinkedIn',
    mailLabel: 'E-mail',
    staticPreviewNote:
        'Preview statique : formulaire et préférences avancées désactivés.',
} satisfies Reference;
