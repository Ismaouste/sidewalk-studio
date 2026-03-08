const NAVIGATION_STORAGE_KEY = 'sidewalk-static-preview:navigation';
const STALE_NAVIGATION_MS = 8000;

type NavigationPayload = {
    href: string;
    startedAt: number;
};

let installed = false;

export function initializeStaticPreviewNavigation(enabled: boolean): void {
    if (!enabled || installed || typeof window === 'undefined') {
        return;
    }

    installed = true;
    announcePendingNavigation();

    document.addEventListener('click', handleDocumentClick, true);
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
