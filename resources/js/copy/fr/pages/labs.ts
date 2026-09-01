/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/labs').default;

export default {
    activeSurfaces: (count: number) =>
        `${count} surface${count === 1 ? '' : 's'} active${count === 1 ? '' : 's'}`,
    auditCta: 'Parler d’un audit',
    demoEyebrow: 'Démo consentement',
    description:
        'Des terrains d’essai ciblés pour le consentement, les données structurées et les décisions d’interface qui ont encore besoin de pression réelle.',
    exploratoryChip: 'Exploratoire, pas détaché',
    eyebrow: 'Labs',
    labIndex: (index: number) => `Lab ${String(index).padStart(2, '0')}`,
    openLabCta: 'Ouvrir le lab',
    shippedSlicesCta: 'Voir les tranches livrées',
    title: 'Éprouver ce qui est risqué avant que ça touche le code de production.',
} satisfies Reference;
