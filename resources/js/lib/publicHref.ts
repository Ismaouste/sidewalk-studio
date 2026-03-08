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
