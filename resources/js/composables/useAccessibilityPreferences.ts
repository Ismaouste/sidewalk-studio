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
