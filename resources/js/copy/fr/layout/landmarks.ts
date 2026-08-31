/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/layout/landmarks').default;

export default {
    breadcrumb: 'Fil d’Ariane',
    colorTheme: 'Thème de couleur',
    contentMeta: 'Métadonnées du contenu',
    nextStep: 'Étape suivante',
    relatedItems: 'Contenus liés',
} satisfies Reference;
