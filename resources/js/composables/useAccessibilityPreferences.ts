import { readonly, ref } from 'vue';

type MotionPreference = 'full' | 'reduced';
type ContrastPreference = 'default' | 'boost';

const MOTION_STORAGE_KEY = 'sidewalk-accessibility-motion';
const CONTRAST_STORAGE_KEY = 'sidewalk-accessibility-contrast';

const motionPreference = ref<MotionPreference>('full');
const contrastPreference = ref<ContrastPreference>('default');

let initialized = false;

/**
 * Storage access throws rather than returning null in private browsing modes
 * and when a user blocks site data, so every call is guarded. A preference we
 * cannot persist still applies for the current page.
 */
function readStoredPreference(key: string): string | null {
    try {
        return window.localStorage.getItem(key);
    } catch {
        return null;
    }
}

function writeStoredPreference(key: string, value: string): void {
    try {
        window.localStorage.setItem(key, value);
    } catch {
        /* Preference stays in memory for this session only. */
    }
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

function initializeAccessibilityPreferences(): void {
    if (initialized || typeof window === 'undefined') {
        return;
    }

    motionPreference.value =
        readStoredPreference(MOTION_STORAGE_KEY) === 'reduced'
            ? 'reduced'
            : 'full';
    contrastPreference.value =
        readStoredPreference(CONTRAST_STORAGE_KEY) === 'boost'
            ? 'boost'
            : 'default';

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
