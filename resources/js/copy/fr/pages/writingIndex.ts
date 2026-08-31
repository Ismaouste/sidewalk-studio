/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/writingIndex').default;

export default {
    contactCta: 'Prendre contact',
    description:
        "Un flux de journal public avec des articles plus riches et des notes plus courtes autour du build, de l'architecture, du contenu et des terrains qui nourrissent le studio.",
    editorialLabel: 'Articles et notes',
    entryLabelJournal: 'Journal',
    entryLabelNote: 'Note',
    eyebrow: 'Journal',
    newBadge: 'Nouveau',
    newBadgeDescription: 'Publié depuis votre dernière visite',
    nudgeContactCta: 'Échangeons sur un contexte proche',
    projectsCta: 'Voir les références',
    publishedEntriesLabel: (count: number) => `${count} publications`,
    publishedLabel: 'Publié',
    readLabel: 'Lecture',
    title: 'Articles, notes courtes et repères de build.',
} satisfies Reference;
