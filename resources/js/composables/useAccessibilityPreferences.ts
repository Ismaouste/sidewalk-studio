import { readonly, ref } from 'vue';

type MotionPreference = 'full' | 'reduced';
type ContrastPreference = 'default' | 'boost';

const MOTION_STORAGE_KEY = 'sidewalk-accessibility-motion';
const CONTRAST_STORAGE_KEY = 'sidewalk-accessibility-contrast';

const motionPreference = ref<MotionPreference>('full');
const contrastPreference = ref<ContrastPreference>('default');

let initialized = false;

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

    const storedMotion = window.localStorage.getItem(MOTION_STORAGE_KEY);
    const storedContrast = window.localStorage.getItem(CONTRAST_STORAGE_KEY);

    motionPreference.value =
        storedMotion === 'reduced' ? 'reduced' : 'full';
    contrastPreference.value =
        storedContrast === 'boost' ? 'boost' : 'default';

    applyPreferences();
    initialized = true;
}

export function useAccessibilityPreferences() {
    initializeAccessibilityPreferences();

    function setMotionPreference(preference: MotionPreference): void {
        motionPreference.value = preference;
        applyPreferences();
        window.localStorage.setItem(MOTION_STORAGE_KEY, preference);
    }

    function setContrastPreference(preference: ContrastPreference): void {
        contrastPreference.value = preference;
        applyPreferences();
        window.localStorage.setItem(CONTRAST_STORAGE_KEY, preference);
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
