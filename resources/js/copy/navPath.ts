import * as en from './en';

/**
 * The locale-stripped paths the navigation copy covers. Derived from the
 * English table rather than declared twice, so adding a route to the copy files
 * is enough — nothing here needs editing to keep them in step.
 */
export type NavPath = keyof typeof en.layout.navigation.action;

/** Narrows an arbitrary locale-stripped path to one the copy table covers. */
export function isNavPath(value: string): value is NavPath {
    return value in en.layout.navigation.action;
}
