const localeMap: Record<string, string> = {
    fr: 'fr-FR',
    en: 'en-GB',
};

type Variant = 'month' | 'long';

function parseIsoDate(value: string): { year: number; month: number; day: number } | null {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value.trim());

    if (!match) {
        return null;
    }

    const [, year, month, day] = match;

    return {
        year: Number(year),
        month: Number(month),
        day: Number(day),
    };
}

export function formatPublicDate(
    value: string | null | undefined,
    locale: string,
    variant: Variant = 'month',
): string {
    if (!value) {
        return '';
    }

    const parsed = parseIsoDate(value);

    if (!parsed) {
        return value;
    }

    const date = new Date(Date.UTC(parsed.year, parsed.month - 1, parsed.day));
    const resolvedLocale = localeMap[locale] ?? localeMap.en;
    const options: Intl.DateTimeFormatOptions =
        variant === 'long'
            ? {
                  day: 'numeric',
                  month: 'long',
                  year: 'numeric',
                  timeZone: 'UTC',
              }
            : {
                  month: 'long',
                  year: 'numeric',
                  timeZone: 'UTC',
              };

    return new Intl.DateTimeFormat(resolvedLocale, options).format(date);
}
