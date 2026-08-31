import type { PublicLocale } from '@/types';

/**
 * The action verb shown alongside each primary nav entry ("Enter", "Write").
 *
 * Hoisted to module scope so the two literals are allocated once rather than
 * on every render pass, and keyed by locale so a route added to one language
 * without the other is a compile error. See `copy/home.ts` for the rationale.
 */
export type NavPath = '/' | '/local' | '/projects' | '/journal' | '/contact';

export const navActionCopy: Record<PublicLocale, Record<NavPath, string>> = {
    fr: {
        '/': 'Entrer',
        '/local': 'Voir la base',
        '/projects': 'Voir le parcours',
        '/journal': 'Lire les notes',
        '/contact': 'Écrire',
    },
    en: {
        '/': 'Enter',
        '/local': 'Read local',
        '/projects': 'View work',
        '/journal': 'Read notes',
        '/contact': 'Write',
    },
};

/** Narrows an arbitrary locale-stripped path to one the copy table covers. */
export function isNavPath(value: string): value is NavPath {
    return value in navActionCopy.en;
}
