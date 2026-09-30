{{-- The XML declaration is echoed, not written literally: with short_open_tag on (the Vercel PHP runtime), a raw `<?xml` is parsed as PHP and the page answers 500. --}}
{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($entries as $entry)
    <url>
        <loc>{{ $entry['loc'] }}</loc>
        <lastmod>{{ $entry['lastmod'] }}</lastmod>
    </url>
@endforeach
</urlset>
