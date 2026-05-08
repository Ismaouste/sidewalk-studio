<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script>
            (() => {
                const stored = window.localStorage.getItem('sidewalk-theme');
                const theme =
                    stored === 'morning' || stored === 'sunset'
                        ? stored
                        : window.matchMedia('(prefers-color-scheme: dark)')
                                .matches
                          ? 'sunset'
                          : 'morning';

                document.documentElement.setAttribute('data-theme', theme);
                document.documentElement.style.colorScheme =
                    theme === 'sunset' ? 'dark' : 'light';
            })();
        </script>

        <title inertia>{{ $seo['title'] ?? config('site.name') }}</title>
        <meta name="description" content="{{ $seo['description'] ?? config('site.description') }}">
        <meta name="robots" content="{{ $seo['robots'] ?? 'index,follow' }}">
        <link rel="canonical" href="{{ $seo['canonical'] ?? config('site.url') }}">

        <meta property="og:title" content="{{ $seo['openGraph']['title'] ?? ($seo['title'] ?? config('site.name')) }}">
        <meta property="og:description" content="{{ $seo['openGraph']['description'] ?? ($seo['description'] ?? config('site.description')) }}">
        <meta property="og:type" content="{{ $seo['openGraph']['type'] ?? 'website' }}">
        <meta property="og:url" content="{{ $seo['openGraph']['url'] ?? ($seo['canonical'] ?? config('site.url')) }}">
        <meta property="og:site_name" content="{{ $seo['openGraph']['site_name'] ?? config('site.name') }}">
        <meta property="og:locale" content="{{ $seo['openGraph']['locale'] ?? config('site.locale') }}">
        @if (!empty($seo['openGraph']['image']))
            <meta property="og:image" content="{{ $seo['openGraph']['image'] }}">
            <meta property="og:image:alt" content="{{ $seo['openGraph']['image_alt'] ?? ($seo['title'] ?? config('site.name')) }}">
        @endif

        <meta name="twitter:card" content="{{ $seo['twitter']['card'] ?? 'summary_large_image' }}">
        <meta name="twitter:title" content="{{ $seo['twitter']['title'] ?? ($seo['title'] ?? config('site.name')) }}">
        <meta name="twitter:description" content="{{ $seo['twitter']['description'] ?? ($seo['description'] ?? config('site.description')) }}">
        @if (!empty($seo['twitter']['image']))
            <meta name="twitter:image" content="{{ $seo['twitter']['image'] }}">
            <meta name="twitter:image:alt" content="{{ $seo['twitter']['image_alt'] ?? ($seo['title'] ?? config('site.name')) }}">
        @endif

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @if (!empty($seo['jsonLd']))
            @foreach ($seo['jsonLd'] as $schema)
                <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
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
