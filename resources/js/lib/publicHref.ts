export function resolvePublicHref(
    href: string,
    staticPreview: boolean,
    staticBasePath?: string | null,
): string {
    const sanitizedHref = sanitizePublicHref(href);

    if (!sanitizedHref.startsWith('/') || !staticPreview) {
        return sanitizedHref;
    }

    const normalizedBase = normalizeBasePath(staticBasePath);

    if (!normalizedBase || normalizedBase === '/') {
        return sanitizedHref;
    }

    if (sanitizedHref === '/') {
        return normalizedBase;
    }

    return sanitizedHref.startsWith(normalizedBase)
        ? sanitizedHref
        : `${normalizedBase}${sanitizedHref.slice(1)}`;
}

export function localizePublicHref(href: string, locale: string): string {
    if (!href.startsWith('/')) {
        return href;
    }

    const [pathPart, fragmentPart] = sanitizePublicHref(href).split('#', 2);
    const [pathname, queryString] = pathPart.split('?', 2);
    const normalizedPath = stripLocalePrefix(pathname || '/');
    const localizedPath = normalizedPath === '/'
        ? `/${locale}`
        : `/${locale}${normalizedPath}`;
    const query = queryString ? `?${queryString}` : '';
    const fragment = fragmentPart ? `#${fragmentPart}` : '';

    return `${localizedPath}${query}${fragment}`;
}

export function sanitizePublicHref(href: string): string {
    if (!href.includes('?')) {
        return href;
    }

    const [pathPart, fragmentPart] = href.split('#', 2);
    const [pathname, queryString] = pathPart.split('?', 2);

    if (!queryString) {
        return href;
    }

    const params = new URLSearchParams(queryString);
    params.delete('path');
    params.delete('lang');

    const normalizedQuery = params.toString();
    const fragment = fragmentPart ? `#${fragmentPart}` : '';

    return normalizedQuery
        ? `${pathname}?${normalizedQuery}${fragment}`
        : `${pathname}${fragment}`;
}

function normalizeBasePath(value?: string | null): string {
    if (!value) {
        return '/';
    }

    const trimmed = value.trim();

    if (!trimmed || trimmed === '/') {
        return '/';
    }

    return `/${trimmed.replace(/^\/+|\/+$/g, '')}/`;
}

function stripLocalePrefix(pathname: string): string {
    const normalized = pathname.startsWith('/') ? pathname : `/${pathname}`;

    for (const locale of ['en', 'fr']) {
        if (normalized === `/${locale}`) {
            return '/';
        }

        if (normalized.startsWith(`/${locale}/`)) {
            return normalized.slice(locale.length + 1) || '/';
        }
    }

    return normalized === '' ? '/' : normalized;
}
