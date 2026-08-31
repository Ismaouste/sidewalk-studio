/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/layout/reading').default;

export default {
    resumeAction: 'Reprendre là',
    resumeDismiss: 'Repartir du début',
    resumeNote: 'Mémorisé dans ce navigateur uniquement.',
    resumeTitle: (percent: number) =>
        `Vous en étiez à ${percent}% la dernière fois.`,
} satisfies Reference;
