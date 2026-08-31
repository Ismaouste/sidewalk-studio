/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/caseStudiesIndex').default;

export default {
    contactCta: 'Discuter un build similaire',
    description:
        "Des cas plus précis autour des flux produit, de l'auto-hébergement, du consentement, du SEO technique et des systèmes web qui demandent de la tenue.",
    eyebrow: 'Études de cas',
    internalBuildLabel: 'Build interne',
    projectsCta: 'Lire les expériences',
    publicSlicesLabel: (count: number) => `${count} cas publiés`,
    publishedLabel: 'Publié',
    reviewLabel: 'Format revue technique',
    stackLabel: 'Stack',
    title: 'Décisions techniques, outils utiles, détails qui comptent.',
    toolsSuffix: 'outils',
} satisfies Reference;
