import type { AnalyticsDriver, AnalyticsServices } from '@/types';

type AnalyticsEnabledEvent = CustomEvent<{ driver: AnalyticsDriver }>;

const SCRIPT_ATTR = 'data-sidewalk-analytics';
let services: AnalyticsServices | null = null;

function honorsDoNotTrack(): boolean {
    if (typeof navigator === 'undefined') {
        return false;
    }
    const dnt =
        navigator.doNotTrack ??
        (window as unknown as { doNotTrack?: string }).doNotTrack;
    return dnt === '1' || dnt === 'yes';
}

function removeManagedScripts(): void {
    document
        .querySelectorAll(`script[${SCRIPT_ATTR}]`)
        .forEach((node) => node.remove());
}

function injectScript(src: string, attrs: Record<string, string> = {}): void {
    if (document.querySelector(`script[${SCRIPT_ATTR}][src="${src}"]`)) {
        return;
    }
    const script = document.createElement('script');
    script.src = src;
    script.defer = true;
    script.setAttribute(SCRIPT_ATTR, 'true');
    Object.entries(attrs).forEach(([key, value]) => {
        script.setAttribute(key, value);
    });
    document.head.appendChild(script);
}

function loadUmami(): void {
    const config = services?.umami;
    if (!config?.website_id || !config.script_url) {
        return;
    }
    injectScript(config.script_url, {
        'data-website-id': config.website_id,
    });
}

function loadVercel(): void {
    if (!services?.vercel?.enabled) {
        return;
    }
    injectScript('/_vercel/insights/script.js');
    injectScript('/_vercel/speed-insights/script.js');
}

function handleEnabled(event: Event): void {
    if (honorsDoNotTrack()) {
        return;
    }
    const driver = (event as AnalyticsEnabledEvent).detail?.driver;
    switch (driver) {
        case 'umami':
            loadUmami();
            break;
        case 'vercel':
            loadVercel();
            break;
    }
}

function handleDisabled(): void {
    removeManagedScripts();
}

export function setupAnalyticsAdapters(
    analyticsServices: AnalyticsServices | undefined,
): void {
    services = analyticsServices ?? null;
    window.addEventListener('sidewalk:analytics:enabled', handleEnabled);
    window.addEventListener('sidewalk:analytics:disabled', handleDisabled);
}
