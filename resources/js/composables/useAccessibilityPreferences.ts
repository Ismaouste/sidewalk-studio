import { readonly, ref } from 'vue';
import { readStorage, writeStorage } from '@/lib/safeStorage';

type MotionPreference = 'full' | 'reduced';
type ContrastPreference = 'default' | 'boost';

const MOTION_STORAGE_KEY = 'sidewalk-accessibility-motion';
const CONTRAST_STORAGE_KEY = 'sidewalk-accessibility-contrast';

const motionPreference = ref<MotionPreference>('full');
const contrastPreference = ref<ContrastPreference>('default');

let initialized = false;

// A preference we cannot persist still applies for the current page; the
// guard that makes that true lives in `safeStorage`.
function readStoredPreference(key: string): string | null {
    return readStorage('local', key);
}

function writeStoredPreference(key: string, value: string): void {
    writeStorage('local', key, value);
}

function applyPreferences(): void {
    if (typeof document === 'undefined') {
        return;
    }

    document.documentElement.setAttribute(
        'data-motion',
        motionPreference.value,
    );
    document.documentElement.setAttribute(
        'data-contrast',
        contrastPreference.value,
    );
}

// An explicit stored choice wins over the system one in both directions:
// someone who turned motion back on asked for it. Only the absence of a
// stored value falls through to the media query. Testing for one value alone
// -- which is what this did -- cannot tell a deliberate opt-out from a
// visitor who has never touched the control, and so reported motion as full
// to everyone who had only ever set it at the operating system.
//
// `app.blade.php` resolves the same three preferences the same way before
// first paint. This runs after hydration and must agree with it, or the panel
// would report a state the document does not have.
function resolvePreference<T extends string>(
    stored: string | null,
    values: readonly [T, T],
    query: string,
): T {
    if (values.includes(stored as T)) {
        return stored as T;
    }

    return window.matchMedia(query).matches ? values[0] : values[1];
}

function initializeAccessibilityPreferences(): void {
    if (initialized || typeof window === 'undefined') {
        return;
    }

    motionPreference.value = resolvePreference(
        readStoredPreference(MOTION_STORAGE_KEY),
        ['reduced', 'full'],
        '(prefers-reduced-motion: reduce)',
    );
    contrastPreference.value = resolvePreference(
        readStoredPreference(CONTRAST_STORAGE_KEY),
        ['boost', 'default'],
        '(prefers-contrast: more)',
    );

    applyPreferences();
    initialized = true;
}

export function useAccessibilityPreferences() {
    initializeAccessibilityPreferences();

    function setMotionPreference(preference: MotionPreference): void {
        motionPreference.value = preference;
        applyPreferences();
        writeStoredPreference(MOTION_STORAGE_KEY, preference);
    }

    function setContrastPreference(preference: ContrastPreference): void {
        contrastPreference.value = preference;
        applyPreferences();
        writeStoredPreference(CONTRAST_STORAGE_KEY, preference);
    }

    function toggleReducedMotion(): void {
        setMotionPreference(
            motionPreference.value === 'reduced' ? 'full' : 'reduced',
        );
    }

    function toggleBoostContrast(): void {
        setContrastPreference(
            contrastPreference.value === 'boost' ? 'default' : 'boost',
        );
    }

    return {
        motionPreference: readonly(motionPreference),
        contrastPreference: readonly(contrastPreference),
        setMotionPreference,
        setContrastPreference,
        toggleReducedMotion,
        toggleBoostContrast,
    };
}
