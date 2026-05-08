const NAVIGATION_STORAGE_KEY = 'sidewalk-static-preview:navigation';
const STALE_NAVIGATION_MS = 8000;
const PREFETCH_INTENT_DEBOUNCE_MS = 100;
const PREFETCHED_URLS = new Set<string>();

type NavigationPayload = {
    href: string;
    startedAt: number;
};

type NetworkInformation = {
    saveData?: boolean;
    effectiveType?: '4g' | '3g' | '2g' | 'slow-2g';
};

type ConnectedNavigator = Navigator & {
    connection?: NetworkInformation;
};

let installed = false;
let serviceWorkerRegistered = false;
let prefetchIntentTimer: number | undefined;

function shouldPrefetch(): boolean {
    if (typeof navigator === 'undefined') {
        return false;
    }

    const conn = (navigator as ConnectedNavigator).connection;

    if (!conn) {
        return true;
    }

    if (conn.saveData) {
        return false;
    }

    if (conn.effectiveType && conn.effectiveType !== '4g') {
        return false;
    }

    return true;
}

export function initializeStaticPreviewNavigation(
    enabled: boolean,
    basePath: string | null = '/',
): void {
    if (!enabled || installed || typeof window === 'undefined') {
        return;
    }

    installed = true;
    announcePendingNavigation();
    registerStaticPreviewServiceWorker(basePath);

    document.addEventListener('click', handleDocumentClick, true);
    document.addEventListener('pointerenter', handlePrefetchIntent, true);
    document.addEventListener('focusin', handlePrefetchIntent, true);
    document.addEventListener('touchstart', handlePrefetchIntent, {
        capture: true,
        passive: true,
    });
    window.addEventListener('pageshow', announcePendingNavigation);
}

export function clearStaticPreviewNavigation(): void {
    if (typeof window === 'undefined') {
        return;
    }

    window.sessionStorage.removeItem(NAVIGATION_STORAGE_KEY);
}

function announcePendingNavigation(): void {
    const payload = readNavigationPayload();

    if (!payload) {
        clearStaticPreviewNavigation();
        return;
    }

    window.dispatchEvent(
        new CustomEvent('sidewalk:static-preview-arrive', {
            detail: payload,
        }),
    );
}

function handleDocumentClick(event: MouseEvent): void {
    if (
        event.defaultPrevented ||
        event.button !== 0 ||
        event.metaKey ||
        event.ctrlKey ||
        event.shiftKey ||
        event.altKey
    ) {
        return;
    }

    const target = event.target;

    if (!(target instanceof Element)) {
        return;
    }

    const anchor = target.closest('a[href]');

    if (!(anchor instanceof HTMLAnchorElement)) {
        return;
    }

    const href = anchor.getAttribute('href');

    if (!href || !shouldHandleAnchor(anchor, href)) {
        return;
    }

    event.preventDefault();

    const payload: NavigationPayload = {
        href: anchor.href,
        startedAt: Date.now(),
    };

    window.sessionStorage.setItem(
        NAVIGATION_STORAGE_KEY,
        JSON.stringify(payload),
    );

    window.dispatchEvent(
        new CustomEvent('sidewalk:static-preview-start', {
            detail: payload,
        }),
    );

    window.setTimeout(() => {
        window.location.assign(payload.href);
    }, 36);
}

function shouldHandleAnchor(
    anchor: HTMLAnchorElement,
    rawHref: string,
): boolean {
    if (
        rawHref.startsWith('#') ||
        rawHref.startsWith('mailto:') ||
        rawHref.startsWith('tel:') ||
        rawHref.startsWith('javascript:') ||
        anchor.hasAttribute('download')
    ) {
        return false;
    }

    if (anchor.target && anchor.target !== '_self') {
        return false;
    }

    const url = new URL(anchor.href, window.location.href);

    if (url.origin !== window.location.origin) {
        return false;
    }

    if (/\.(pdf|png|jpe?g|svg|webp|gif|ico|xml|txt)$/i.test(url.pathname)) {
        return false;
    }

    if (
        url.pathname === window.location.pathname &&
        url.search === window.location.search &&
        url.hash === window.location.hash
    ) {
        return false;
    }

    return true;
}

function handlePrefetchIntent(event: Event): void {
    const target = event.target;

    if (!(target instanceof Element)) {
        return;
    }

    const anchor = target.closest('a[href]');

    if (!(anchor instanceof HTMLAnchorElement)) {
        return;
    }

    if (prefetchIntentTimer !== undefined) {
        window.clearTimeout(prefetchIntentTimer);
    }

    prefetchIntentTimer = window.setTimeout(() => {
        prefetchIntentTimer = undefined;
        prefetchAnchor(anchor);
    }, PREFETCH_INTENT_DEBOUNCE_MS);
}

function prefetchAnchor(anchor: HTMLAnchorElement): void {
    const href = anchor.getAttribute('href');

    if (!href || !shouldHandleAnchor(anchor, href)) {
        return;
    }

    if (!shouldPrefetch()) {
        return;
    }

    const url = new URL(anchor.href, window.location.href);
    const cacheKey = url.href;

    if (PREFETCHED_URLS.has(cacheKey)) {
        return;
    }

    PREFETCHED_URLS.add(cacheKey);
    appendPrefetchHint(url.href);

    void fetch(url.href, {
        credentials: 'same-origin',
        headers: {
            'X-Static-Preview-Prefetch': '1',
        },
    }).catch(() => {
        PREFETCHED_URLS.delete(cacheKey);
    });
}

function appendPrefetchHint(href: string): void {
    if (!document.head) {
        return;
    }

    const existing = document.head.querySelector(
        `link[rel="prefetch"][href="${href}"]`,
    );

    if (existing) {
        return;
    }

    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = href;
    link.as = 'document';
    document.head.appendChild(link);
}

function registerStaticPreviewServiceWorker(basePath: string | null): void {
    if (
        serviceWorkerRegistered ||
        typeof navigator === 'undefined' ||
        !('serviceWorker' in navigator)
    ) {
        return;
    }

    serviceWorkerRegistered = true;
    const normalizedBasePath = normalizeBasePath(basePath);

    window.addEventListener('load', () => {
        void navigator.serviceWorker
            .register(`${normalizedBasePath}sw.js`, {
                scope: normalizedBasePath,
            })
            .catch(() => {
                serviceWorkerRegistered = false;
            });
    });
}

function normalizeBasePath(basePath: string | null): string {
    if (!basePath || basePath === '/') {
        return '/';
    }

    return `/${basePath.replace(/^\/+|\/+$/g, '')}/`;
}

function readNavigationPayload(): NavigationPayload | null {
    const raw = window.sessionStorage.getItem(NAVIGATION_STORAGE_KEY);

    if (!raw) {
        return null;
    }

    try {
        const payload = JSON.parse(raw) as Partial<NavigationPayload>;

        if (
            typeof payload.href !== 'string' ||
            typeof payload.startedAt !== 'number'
        ) {
            return null;
        }

        if (Date.now() - payload.startedAt > STALE_NAVIGATION_MS) {
            return null;
        }

        return {
            href: payload.href,
            startedAt: payload.startedAt,
        };
    } catch {
        return null;
    }
}
