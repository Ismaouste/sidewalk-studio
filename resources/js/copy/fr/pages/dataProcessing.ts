/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/dataProcessing').default;

export default {
    audienceOptOutHint:
        'Mémorisé dans ce navigateur uniquement. Global Privacy Control est respecté automatiquement.',
    audienceOptOutLabel: "Me retirer du ping d'audience anonyme",
    openPreferences: 'Ouvrir les préférences de consentement',
    replayHint:
        'Désactivé par défaut, jamais inclus dans « Tout accepter ». Vaut pour ce navigateur uniquement.',
    replayHintConsentNeeded:
        "Acceptez d'abord la catégorie analytics — la relecture s'appuie dessus.",
    replayLabel: 'Autoriser la relecture de session et les cartes de chaleur',
} satisfies Reference;
