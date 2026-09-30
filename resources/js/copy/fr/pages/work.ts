/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here. Keys are sorted; `sort-keys`
 * enforces it in lint.
 */
type Reference = typeof import('../../en/pages/work').default;

export default {
    lotLabel: 'Lot',
    noLink: "Pas encore d'adresse publique",
    revisionLabel: 'Indice',
    roleLabel: "Ce que j'ai fait",
} satisfies Reference;
