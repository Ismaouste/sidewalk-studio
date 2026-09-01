/**
 * The audit lead magnet's page chrome. The report vocabulary (metric
 * ratings) mirrors the audit_mail lang group so the page and the email
 * describe the same measurement in the same words.
 *
 * English copy. This is the reference shape the French file is checked
 * against. Keys are sorted; `sort-keys` enforces it in lint.
 */
export default {
    emailLabel: 'Where should the report go?',
    emailPlaceholder: 'you@example.com',
    errorNote:
        'The measurement did not complete — PageSpeed may be busy. Try again in a minute.',
    eyebrow: 'Labs · Lead magnet',
    fieldHeading: 'Real-visitor Core Web Vitals (p75)',
    metricRatings: {
        AVERAGE: 'Needs work',
        FAST: 'Good',
        NONE: 'No data',
        SLOW: 'Poor',
    } as Record<string, string>,
    noFieldData:
        'Google has no field data for this site yet — the lab snapshot in your emailed report still applies.',
    opportunitiesHeading: 'Highest-impact fixes',
    performanceLabel: 'Performance',
    privacyNote:
        'Your email is used once, to send this report. No list, no follow-up without your say-so.',
    runningNote:
        'Measuring with PageSpeed Insights — a real mobile run takes 20 to 40 seconds.',
    scoresHeading: 'Lighthouse scores (mobile)',
    sentNote: 'The full report is in your inbox. Here is the short version:',
    seoLabel: 'SEO',
    submitCta: 'Audit my site',
    summary:
        'The server calls PageSpeed Insights, folds the CrUX field data into a mini-report, and emails it to you. A lead magnet, a tech demo, and a journal post — the same 200 lines.',
    title: 'A Core Web Vitals + SEO audit, by email, in under a minute.',
    urlLabel: 'Site to audit',
    urlPlaceholder: 'https://your-shop.example',
};
