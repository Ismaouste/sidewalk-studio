/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/newsletterConfirmed').default;

export default {
    backHomeCta: 'Revenir au site',
    eyebrow: 'Newsletter',
    journalCta: 'Lire le journal',
    summary:
        'Votre inscription est active. Le prochain numéro arrivera directement — d’ici là, le journal et les études de cas vous sont ouverts.',
    title: 'C’est confirmé. Merci !',
} satisfies Reference;
