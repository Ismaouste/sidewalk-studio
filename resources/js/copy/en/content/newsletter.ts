/**
 * The newsletter signup block. One component, three placements — the copy
 * is stage-aware (V1 readers on journal posts, V2 prospects on Home and
 * case studies) while the mechanics stay identical.
 *
 * English copy. This is the reference shape the French file is checked
 * against. Keys are sorted; `sort-keys` enforces it in lint.
 */
export default {
    contexts: {
        'case-study': {
            eyebrow: 'Newsletter',
            summary:
                'One email when a new case study or a practical growth playbook ships. No noise between releases.',
            title: 'Read the next build before your competitors do.',
        },
        home: {
            eyebrow: 'Newsletter',
            summary:
                'Occasional, concrete notes on what makes a local site sell: speed, product data, campaigns that respect consent.',
            title: 'Web advice for shops that ship.',
        },
        journal: {
            eyebrow: 'Newsletter',
            summary:
                'New journal posts and engineering write-ups, straight from the repo to your inbox. Nothing else.',
            title: 'Get the next post by email.',
        },
    },
    emailLabel: 'Email address',
    emailPlaceholder: 'you@example.com',
    errorNote: 'That did not go through. Please try again in a minute.',
    pendingNote:
        'One more step — a confirmation email is on its way. The subscription only starts once you click it.',
    privacyNote: 'Double opt-in. One-click unsubscribe. No tracking pixels.',
    submitCta: 'Subscribe',
    submittingCta: 'Sending…',
};
