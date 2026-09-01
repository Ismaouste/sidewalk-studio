<?php

namespace App\Audit;

use Illuminate\Support\Facades\Http;
use Throwable;

final class PageSpeedClient
{
    /**
     * @return array<string, mixed>
     *
     * @throws AuditUnavailableException
     */
    public function run(string $url, string $locale): array
    {
        // PSI expects the category parameter repeated, which
        // http_build_query would mangle into category[0]= — so the query
        // string is assembled by hand.
        $parts = [
            'url='.rawurlencode($url),
            'strategy=mobile',
            'category=PERFORMANCE',
            'category=SEO',
            'locale='.$locale,
        ];

        $key = config('audit.pagespeed.key');

        if (is_string($key) && $key !== '') {
            $parts[] = 'key='.rawurlencode($key);
        }

        try {
            $response = Http::timeout((int) config('audit.pagespeed.timeout'))
                ->get(config('audit.pagespeed.endpoint').'?'.implode('&', $parts));
        } catch (Throwable $exception) {
            throw new AuditUnavailableException('PageSpeed is unreachable.', 0, $exception);
        }

        if ($response->failed()) {
            throw new AuditUnavailableException('PageSpeed answered '.$response->status());
        }

        return (array) $response->json();
    }
}
