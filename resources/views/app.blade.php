<!DOCTYPE html>
<html lang="{{ config('site.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

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

        <meta name="twitter:card" content="{{ $seo['twitter']['card'] ?? 'summary_large_image' }}">
        <meta name="twitter:title" content="{{ $seo['twitter']['title'] ?? ($seo['title'] ?? config('site.name')) }}">
        <meta name="twitter:description" content="{{ $seo['twitter']['description'] ?? ($seo['description'] ?? config('site.description')) }}">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @if (!empty($seo['jsonLd']))
            @foreach ($seo['jsonLd'] as $schema)
                <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
            @endforeach
        @endif

        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
