import { readonly, ref } from 'vue';
import { readStorage, removeStorage, writeStorage } from '@/lib/safeStorage';

export type ThemeMode = 'morning' | 'sunset';

const STORAGE_KEY = 'sidewalk-theme';
const SYSTEM_QUERY = '(prefers-color-scheme: dark)';
const currentTheme = ref<ThemeMode>('morning');
const hasManualPreference = ref(false);

let initialized = false;
let mediaQueryList: MediaQueryList | null = null;

function resolveSystemTheme(): ThemeMode {
    return window.matchMedia(SYSTEM_QUERY).matches ? 'sunset' : 'morning';
}

function readStoredTheme(): ThemeMode | null {
    const stored = readStorage('local', STORAGE_KEY);

    return stored === 'morning' || stored === 'sunset' ? stored : null;
}

function writeTheme(theme: ThemeMode): void {
    document.documentElement.setAttribute('data-theme', theme);
    document.documentElement.style.colorScheme =
        theme === 'sunset' ? 'dark' : 'light';
}

function handleSystemChange(event: MediaQueryListEvent): void {
    if (hasManualPreference.value) {
        return;
    }

    const theme = event.matches ? 'sunset' : 'morning';
    currentTheme.value = theme;
    writeTheme(theme);
}

export function initializeTheme(defaultTheme: ThemeMode | null = null): void {
    if (initialized || typeof window === 'undefined') {
        return;
    }

    const stored = readStoredTheme();
    const initial = stored ?? defaultTheme ?? resolveSystemTheme();

    currentTheme.value = initial;
    hasManualPreference.value = stored !== null;
    writeTheme(initial);

    mediaQueryList = window.matchMedia(SYSTEM_QUERY);
    mediaQueryList.addEventListener('change', handleSystemChange);
    initialized = true;
}

export function useTheme() {
    initializeTheme();

    function setTheme(theme: ThemeMode): void {
        currentTheme.value = theme;
        hasManualPreference.value = true;
        writeTheme(theme);
        writeStorage('local', STORAGE_KEY, theme);
    }

    function resetTheme(): void {
        hasManualPreference.value = false;
        removeStorage('local', STORAGE_KEY);

        const theme = resolveSystemTheme();
        currentTheme.value = theme;
        writeTheme(theme);
    }

    function toggleTheme(): void {
        setTheme(currentTheme.value === 'morning' ? 'sunset' : 'morning');
    }

    return {
        currentTheme: readonly(currentTheme),
        hasManualPreference: readonly(hasManualPreference),
        setTheme,
        resetTheme,
        toggleTheme,
    };
}
