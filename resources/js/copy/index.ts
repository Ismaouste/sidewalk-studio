import type { PublicLocale } from '@/types';
import * as en from './en';
import * as fr from './fr';

/**
 * All user-facing UI copy, as a tree: `copy/<locale>/<domain>.ts`.
 *
 * Page content proper lives in `resources/content/pages/{fr,en}/*.md` and
 * settings copy in `lang/{en,fr}/site.php`. This tree holds the chrome — button
 * labels, section headings, tooltip definitions, accessibility strings — which
 * used to sit inline in components as `locale === 'fr' ? {…} : {…}` ternaries.
 * A ternary between two object literals infers a union, so a key present in one
 * branch only becomes optional and renders as empty text in the other language.
 *
 * Parity is enforced per domain: each French module ends in
 * `satisfies typeof import('../en/<domain>').default`, which reports both a
 * missing key and an extra one, at the file that drifted. Keys are sorted
 * alphabetically and `sort-keys` enforces that in lint, so the two locales read
 * as parallel columns.
 *
 * Copy that varies with a value is a function rather than a concatenation, so
 * word order stays inside the translation:
 *
 *     taggedThreadsLabel: (count: number) => `${count} fils étiquetés`
 */
export type CopyBundle = typeof en;

export const copy: Record<PublicLocale, CopyBundle> = { en, fr };

/** Resolves the whole bundle for a locale. */
export function copyFor(locale: PublicLocale): CopyBundle {
    return copy[locale];
}
