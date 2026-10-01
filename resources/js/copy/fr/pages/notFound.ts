/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/notFound').default;

export default {
    backHomeCta: 'Retour à l’accueil',
    contactCta: 'Contact',
    eyebrow: 'Erreur 404',
    summary:
        'L’adresse est peut-être mal écrite, ou la page a changé de place. Voici où aller ensuite.',
    title: 'Cette page n’existe pas',
    workCta: 'Voir les réalisations',
} satisfies Reference;
