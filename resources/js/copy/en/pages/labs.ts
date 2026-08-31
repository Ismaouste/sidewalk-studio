/**
 * `/labs` has no Markdown page and no French counterpart, so the language
 * switcher does not offer it — but the route still answers under `/fr`, and
 * a reader who lands there was reading French a moment ago. The chrome is
 * translated for that reader; the lab entries themselves come from
 * `config('site.labs')` and stay a settings surface.
 *
 * English copy. This is the reference shape the French file is checked
 * against. Keys are sorted; `sort-keys` enforces it in lint.
 */
export default {
    activeSurfaces: (count: number) =>
        `${count} active surface${count === 1 ? '' : 's'}`,
    auditCta: 'Discuss an audit',
    demoEyebrow: 'Consent demo',
    description:
        'Focused proving grounds for consent, structured data, and future interface decisions that still need real-world pressure.',
    exploratoryChip: 'Exploratory, not detached',
    eyebrow: 'Labs',
    labIndex: (index: number) => `Lab ${String(index).padStart(2, '0')}`,
    shippedSlicesCta: 'See shipped slices',
    title: 'Sandbox the risky parts before they reach production code.',
};
