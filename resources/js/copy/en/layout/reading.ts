/**
 * English copy. This is the reference shape the French file is checked
 * against. Keys are sorted; `sort-keys` enforces it in lint.
 */
export default {
    resumeAction: 'Take me back there',
    resumeDismiss: 'Start from the top',
    /** Says where the memory lives, because that is the point of it. */
    resumeNote: 'Remembered in this browser only.',
    resumeTitle: (percent: number) =>
        `You were ${percent}% through this last time.`,
};
