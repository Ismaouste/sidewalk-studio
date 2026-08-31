/**
 * What the site remembers about a returning reader, and the only place it is
 * read or written. Everything here lives in that reader's browser: no cookie
 * is set, no request carries any of it, and nothing is recorded server-side.
 * Clearing site data returns the site to its first-visit state, which is the
 * whole point — the privacy claim is checkable in devtools rather than
 * asserted in prose.
 */

import { readStorage, writeStorage } from '@/lib/safeStorage';

const VISIT_KEY = 'sidewalk-last-visit';
const VISIT_SNAPSHOT_KEY = 'sidewalk-visit-snapshot';
const READING_KEY = 'sidewalk-reading-positions';

/**
 * How many articles keep a remembered position. Old entries fall off rather
 * than growing a map nobody prunes.
 */
const REMEMBERED_ARTICLES = 24;

/** Held in the session snapshot to mean "there was no earlier visit". */
const NO_PREVIOUS_VISIT = -1;

/**
 * Below this much of an article, resuming would land the reader roughly where
 * they already are, so there is nothing worth offering.
 */
const MIN_RESUME_RATIO = 0.12;

/**
 * Past this point the reader has effectively finished; dropping them back near
 * the end of something they have read is not a favour.
 */
const MAX_RESUME_RATIO = 0.92;

type ReadingPosition = {
    ratio: number;
    at: number;
};

type ReadingPositions = Record<string, ReadingPosition>;

// A browser that refuses to remember gets a site that does not. `safeStorage`
// is where that guard lives.
function readStored(key: string, session = false): string | null {
    return readStorage(session ? 'session' : 'local', key);
}

function writeStored(key: string, value: string, session = false): void {
    writeStorage(session ? 'session' : 'local', key, value);
}

/**
 * When the reader was last here, frozen for the whole of this visit.
 *
 * It has to be frozen, and in sessionStorage rather than only in memory. The
 * localStorage timestamp is pushed forward as soon as it is read, so a reload
 * would otherwise compare against "a moment ago" and every mark would vanish —
 * which reads as a bug rather than as a feature. sessionStorage holds the
 * answer for the tab, so reloads and client-side navigation all see the same
 * one, while localStorage keeps moving forward for the next visit.
 *
 * `null` means there is no previous visit. A first-ever reader is shown
 * nothing: everything being new is indistinguishable from nothing being new,
 * and marking the whole archive is noise on the one visit where they have the
 * least context for it.
 */
let previousVisitAt: number | null = null;
let initialized = false;

function initialize(): void {
    if (initialized || typeof window === 'undefined') {
        return;
    }

    initialized = true;

    const held = Number.parseInt(
        readStored(VISIT_SNAPSHOT_KEY, true) ?? '',
        10,
    );

    if (Number.isFinite(held)) {
        previousVisitAt = held === NO_PREVIOUS_VISIT ? null : held;

        return;
    }

    const stored = Number.parseInt(readStored(VISIT_KEY) ?? '', 10);
    previousVisitAt = Number.isFinite(stored) ? stored : null;

    // Recorded rather than left absent, so that a first-ever reader still
    // reads as a first-ever reader after a reload instead of suddenly having
    // the whole archive marked new.
    writeStored(
        VISIT_SNAPSHOT_KEY,
        String(previousVisitAt ?? NO_PREVIOUS_VISIT),
        true,
    );
    writeStored(VISIT_KEY, String(Date.now()));
}

function readPositions(): ReadingPositions {
    const raw = readStored(READING_KEY);

    if (raw === null) {
        return {};
    }

    try {
        const parsed: unknown = JSON.parse(raw);

        return parsed !== null && typeof parsed === 'object'
            ? (parsed as ReadingPositions)
            : {};
    } catch {
        // Someone else's data, or ours from an older shape. Start over rather
        // than trying to repair something we cannot identify.
        return {};
    }
}

function writePositions(positions: ReadingPositions): void {
    const trimmed = Object.entries(positions)
        .sort(([, a], [, b]) => b.at - a.at)
        .slice(0, REMEMBERED_ARTICLES);

    writeStored(READING_KEY, JSON.stringify(Object.fromEntries(trimmed)));
}

export function useLocalMemory() {
    initialize();

    /**
     * True when an entry was published after the reader was last here. False
     * on a first visit, and false for anything we cannot date.
     */
    function isNewSinceLastVisit(publishedAt: string | null): boolean {
        if (previousVisitAt === null || !publishedAt) {
            return false;
        }

        const published = Date.parse(publishedAt);

        return Number.isFinite(published) && published > previousVisitAt;
    }

    /**
     * A remembered position worth offering back, or null. The bounds are what
     * keep the invitation from appearing when it would not help.
     */
    function resumableRatio(slug: string): number | null {
        const stored = readPositions()[slug];

        if (stored === undefined || typeof stored.ratio !== 'number') {
            return null;
        }

        return stored.ratio >= MIN_RESUME_RATIO &&
            stored.ratio <= MAX_RESUME_RATIO
            ? stored.ratio
            : null;
    }

    function rememberReadingPosition(slug: string, ratio: number): void {
        if (typeof window === 'undefined' || !Number.isFinite(ratio)) {
            return;
        }

        const positions = readPositions();

        positions[slug] = {
            ratio: Math.min(Math.max(ratio, 0), 1),
            at: Date.now(),
        };

        writePositions(positions);
    }

    function forgetReadingPosition(slug: string): void {
        if (typeof window === 'undefined') {
            return;
        }

        const positions = readPositions();

        if (positions[slug] === undefined) {
            return;
        }

        delete positions[slug];
        writePositions(positions);
    }

    return {
        isNewSinceLastVisit,
        resumableRatio,
        rememberReadingPosition,
        forgetReadingPosition,
    };
}
