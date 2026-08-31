/**
 * Web storage that a browser is allowed to refuse.
 *
 * `window.localStorage` does not merely return null when a visitor blocks site
 * data, or in some private browsing modes: reading the property throws. An
 * unguarded access during boot therefore takes the whole application down
 * before it mounts, and the visitor gets a blank page rather than a site that
 * has forgotten their theme.
 *
 * Everything the site remembers goes through here, so that guard exists once
 * instead of being remembered separately at each call site.
 */

type StorageKind = 'local' | 'session';

function area(kind: StorageKind): Storage | null {
    if (typeof window === 'undefined') {
        return null;
    }

    try {
        return kind === 'local' ? window.localStorage : window.sessionStorage;
    } catch {
        return null;
    }
}

/** The stored value, or null — including when storage itself is unavailable. */
export function readStorage(kind: StorageKind, key: string): string | null {
    try {
        return area(kind)?.getItem(key) ?? null;
    } catch {
        return null;
    }
}

/**
 * Writes if the browser allows it. A refusal is a valid outcome, not an error:
 * the preference still applies for this page, it just will not be there next
 * time.
 */
export function writeStorage(
    kind: StorageKind,
    key: string,
    value: string,
): void {
    try {
        area(kind)?.setItem(key, value);
    } catch {
        /* Nothing is remembered for this browser. */
    }
}

export function removeStorage(kind: StorageKind, key: string): void {
    try {
        area(kind)?.removeItem(key);
    } catch {
        /* Nothing to remove, because nothing was stored. */
    }
}
