/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/caseStudiesShow').default;

export default {
    clientLabel: 'Client',
    contactCta: 'Discuter un brief similaire',
    eyebrow: 'Cas client',
    implementationToolsLabel: (count: number) =>
        `${count} outils implémentation`,
    internalBuildLabel: 'Build interne',
    outcomesLabel: 'Résultats',
    outcomesTitle: 'Résultats',
    projectFrameLabel: 'Cadre projet',
    publishedLabel: 'Publié',
    relatedEyebrow: 'Cas suivants',
    relatedTitle: 'Autres cas clients à explorer',
    roleLabel: 'Rôle',
    signalsSuffix: 'signaux',
    stackLabel: 'Stack',
    updatedLabel: 'Maj',
} satisfies Reference;
