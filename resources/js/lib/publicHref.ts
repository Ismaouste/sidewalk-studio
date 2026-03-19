export function resolvePublicHref(
    href: string,
    staticPreview: boolean,
    staticBasePath?: string | null,
): string {
    if (!href.startsWith('/') || !staticPreview) {
        return href;
    }

    const normalizedBase = normalizeBasePath(staticBasePath);

    if (!normalizedBase || normalizedBase === '/') {
        return href;
    }

    if (href === '/') {
        return normalizedBase;
    }

    return href.startsWith(normalizedBase) ? href : `${normalizedBase}${href.slice(1)}`;
}

export function localizePublicHref(href: string, locale: string): string {
    if (!href.startsWith('/')) {
        return href;
    }

    const [pathPart, fragmentPart] = href.split('#', 2);
    const [pathname, queryString] = pathPart.split('?', 2);
    const normalizedPath = stripLocalePrefix(pathname || '/');
    const localizedPath = normalizedPath === '/'
        ? `/${locale}`
        : `/${locale}${normalizedPath}`;
    const query = queryString ? `?${queryString}` : '';
    const fragment = fragmentPart ? `#${fragmentPart}` : '';

    return `${localizedPath}${query}${fragment}`;
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
