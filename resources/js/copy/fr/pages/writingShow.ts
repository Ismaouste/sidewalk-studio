/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/writingShow').default;

export default {
    caseStudiesCta: 'Ouvrir cas clients',
    contactCta: 'Contact',
    continueDescription:
        "Les cas clients montrent comment les mêmes choix d'architecture se comportent sous pression de livraison et contraintes parties prenantes.",
    continueLabel: 'Poursuivre le fil',
    editorialLabelJournal: 'Article',
    editorialLabelNote: 'Note éditoriale',
    eyebrowJournal: 'Journal',
    eyebrowNote: 'Note',
    publishedLabel: 'Publié',
    readLabel: 'Lecture',
    relatedEyebrow: 'À lire ensuite',
    relatedTitle: 'Autres notes et articles dans le même fil',
    taggedThreadsLabel: (count: number) => `${count} fils étiquetés`,
    updatedLabel: 'Maj',
} satisfies Reference;
