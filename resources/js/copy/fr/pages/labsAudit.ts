/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/labsAudit').default;

export default {
    emailLabel: 'Où envoyer le rapport ?',
    emailPlaceholder: 'vous@exemple.fr',
    errorNote:
        'La mesure n’a pas abouti — PageSpeed est peut-être saturé. Réessayez dans une minute.',
    eyebrow: 'Labs · Lead magnet',
    fieldHeading: 'Core Web Vitals de vos vrais visiteurs (p75)',
    metricRatings: {
        AVERAGE: 'À travailler',
        FAST: 'Bon',
        NONE: 'Pas de donnée',
        SLOW: 'Faible',
    } as Record<string, string>,
    noFieldData:
        'Google n’a pas encore de données terrain pour ce site — l’instantané labo de votre rapport email reste valable.',
    opportunitiesHeading: 'Corrections à plus fort impact',
    performanceLabel: 'Performance',
    privacyNote:
        'Votre email sert une fois, pour envoyer ce rapport. Pas de liste, pas de relance sans votre accord.',
    runningNote:
        'Mesure via PageSpeed Insights — un vrai passage mobile prend 20 à 40 secondes.',
    scoresHeading: 'Scores Lighthouse (mobile)',
    sentNote:
        'Le rapport complet est dans votre boîte mail. Voici la version courte :',
    seoLabel: 'SEO',
    submitCta: 'Auditer mon site',
    summary:
        'Le serveur appelle PageSpeed Insights, replie les données terrain CrUX dans un mini-rapport et vous l’envoie par email. Un lead magnet, une démo technique et un billet de journal — les mêmes 200 lignes.',
    title: 'Un audit Core Web Vitals + SEO, par email, en moins d’une minute.',
    urlLabel: 'Site à auditer',
    urlPlaceholder: 'https://votre-boutique.example',
} satisfies Reference;
