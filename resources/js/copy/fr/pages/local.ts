/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/local').default;

export default {
    contactCta: 'Prendre contact',
    heroChipBase: 'Grand Est',
    heroChipJournal: 'Journal',
    heroChipMobility: 'Mobilité',
    noteLabel: 'Note',
    projectsCta: 'Voir les références',
    publicationLabel: 'Publication',
    publishedLabel: 'Publié',
    readLabel: 'Lecture',
    signalsLabel: 'Ce que je regarde',
} satisfies Reference;
