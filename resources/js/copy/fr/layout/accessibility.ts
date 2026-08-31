/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/layout/accessibility').default;

export default {
    buttonLabel: 'Accessibilité',
    closeLabel: 'Fermer le panneau',
    contrast: 'Contraste renforcé',
    contrastHint:
        'Textes secondaires et bordures plus appuyés sur les deux thèmes.',
    panelLabel: "Réglages d'accessibilité",
    reducedMotion: 'Animations réduites',
    reducedMotionHint: 'Fond ambient, loader et transitions plus sobres.',
    stateOff: 'Off',
    stateOn: 'On',
} satisfies Reference;
