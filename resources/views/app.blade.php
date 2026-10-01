<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script>
            (() => {
                // Reading localStorage throws, rather than returning null,
                // when a visitor blocks site data. This runs before anything
                // else on the page, so an unguarded read here takes the
                // document down with it.
                const read = (key) => {
                    try {
                        return window.localStorage.getItem(key);
                    } catch (error) {
                        return null;
                    }
                };

                // One shape for all three: an explicit stored choice wins in
                // both directions, and only its absence falls through to what
                // the system asks for. Testing for one value alone would read
                // a deliberate opt-out as an absent preference.
                const resolve = (stored, values, query, whenMatched) =>
                    values.includes(stored)
                        ? stored
                        : window.matchMedia(query).matches
                          ? whenMatched
                          : values[values.length - 1];

                const theme = resolve(
                    read('sidewalk-theme'),
                    ['sunset', 'morning'],
                    '(prefers-color-scheme: dark)',
                    'sunset',
                );

                // Seeded here rather than at hydration, for the reason the
                // theme is: an attribute that lands after the first paint
                // lands too late to stop the animation it exists to prevent.
                const motion = resolve(
                    read('sidewalk-accessibility-motion'),
                    ['reduced', 'full'],
                    '(prefers-reduced-motion: reduce)',
                    'reduced',
                );

                const contrast = resolve(
                    read('sidewalk-accessibility-contrast'),
                    ['boost', 'default'],
                    '(prefers-contrast: more)',
                    'boost',
                );

                document.documentElement.setAttribute('data-theme', theme);
                document.documentElement.setAttribute('data-motion', motion);
                document.documentElement.setAttribute(
                    'data-contrast',
                    contrast,
                );
                document.documentElement.style.colorScheme =
                    theme === 'sunset' ? 'dark' : 'light';
            })();
        </script>

        <title data-inertia>{{ $seo['title'] ?? config('site.name') }}</title>
        <meta data-inertia name="description" content="{{ $seo['description'] ?? config('site.description') }}">
        <meta data-inertia name="robots" content="{{ $seo['robots'] ?? 'index,follow' }}">
        <link data-inertia rel="canonical" href="{{ $seo['canonical'] ?? config('site.url') }}">

        <meta data-inertia property="og:title" content="{{ $seo['openGraph']['title'] ?? ($seo['title'] ?? config('site.name')) }}">
        <meta data-inertia property="og:description" content="{{ $seo['openGraph']['description'] ?? ($seo['description'] ?? config('site.description')) }}">
        <meta data-inertia property="og:type" content="{{ $seo['openGraph']['type'] ?? 'website' }}">
        <meta data-inertia property="og:url" content="{{ $seo['openGraph']['url'] ?? ($seo['canonical'] ?? config('site.url')) }}">
        <meta data-inertia property="og:site_name" content="{{ $seo['openGraph']['site_name'] ?? config('site.name') }}">
        <meta data-inertia property="og:locale" content="{{ $seo['openGraph']['locale'] ?? config('site.locale') }}">
        @if (!empty($seo['openGraph']['image']))
            <meta data-inertia property="og:image" content="{{ $seo['openGraph']['image'] }}">
            <meta data-inertia property="og:image:alt" content="{{ $seo['openGraph']['image_alt'] ?? ($seo['title'] ?? config('site.name')) }}">
        @endif

        <meta data-inertia name="twitter:card" content="{{ $seo['twitter']['card'] ?? 'summary_large_image' }}">
        <meta data-inertia name="twitter:title" content="{{ $seo['twitter']['title'] ?? ($seo['title'] ?? config('site.name')) }}">
        <meta data-inertia name="twitter:description" content="{{ $seo['twitter']['description'] ?? ($seo['description'] ?? config('site.description')) }}">
        @if (!empty($seo['twitter']['image']))
            <meta data-inertia name="twitter:image" content="{{ $seo['twitter']['image'] }}">
            <meta data-inertia name="twitter:image:alt" content="{{ $seo['twitter']['image_alt'] ?? ($seo['title'] ?? config('site.name')) }}">
        @endif

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preload" href="/fonts/Switzer-Variable.woff2" as="font" type="font/woff2" crossorigin>

        @if (!empty($seo['jsonLd']))
            @foreach ($seo['jsonLd'] as $schema)
                <script type="application/ld+json" data-inertia>{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
            @endforeach
        @endif

        {{-- Speculation Rules: prefetch internal links on Chromium browsers
             with moderate eagerness. Not prerender — that would execute
             scripts before vanilla-cookieconsent gates them. Browsers
             without support ignore the script silently. --}}
        <script type="speculationrules">
        {
            "prefetch": [
                {
                    "where": {
                        "and": [
                            { "href_matches": "/*" },
                            { "not": { "href_matches": "/*.pdf" } },
                            { "not": { "href_matches": "/*.zip" } },
                            { "not": { "href_matches": "/admin/*" } }
                        ]
                    },
                    "eagerness": "moderate"
                }
            ]
        }
        </script>

        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
