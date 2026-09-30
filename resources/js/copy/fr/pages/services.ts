/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/services').default;

export default {
    contactCta: 'Contact',
    rangesLabel: 'Tarifs de départ',
    stacksLabel: 'Stacks',
} satisfies Reference;
