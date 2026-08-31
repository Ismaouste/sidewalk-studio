/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/projects').default;

export default {
    contactCta: "Discuter d'un contexte proche",
    focusAreasLabel: 'Domaines',
    hobbiesLabel: 'À côté du travail',
    lookingForLabel: 'Ce que je recherche',
    nudgeJournalCta: 'Continuer vers le journal',
    openerEyebrow: 'Comment je travaille',
    overviewCta: 'Découvrir toutes les études de cas',
    signageAriaLabel: 'Aller à un projet',
    spreadStackLabel: 'Stack',
    strengthsLabel: 'Forces',
    trajectoryLabel: 'Parcours',
} satisfies Reference;
