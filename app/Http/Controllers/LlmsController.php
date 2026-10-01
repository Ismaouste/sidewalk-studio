<?php

namespace App\Http\Controllers;

use App\Services\SiteSettingsService;
use App\Support\PublicLocale;
use Illuminate\Http\Response;

/**
 * `/llms.txt`: what the site is, in a few lines and a list of links, for answer
 * engines and agents (llmstxt.org). Everything in it comes from the site settings
 * and the navigation, so it never says anything the site does not.
 */
class LlmsController extends Controller
{
    public function __invoke(SiteSettingsService $settings): Response
    {
        $site = $settings->current();
        $name = $site->siteIdentity->name;
        $base = rtrim((string) config('site.url'), '/');
        $author = (array) config('site.author');
        $lines = [
            "# {$name}",
            '',
            '> '.trim((string) $site->seoDefaults->defaultDescription),
            '',
        ];

        if (! empty($author['job_title'])) {
            $lines[] = "{$name}: {$author['job_title']}.";
            $lines[] = '';
        }

        $lines[] = '## Pages';

        foreach ((array) config('site.navigation') as $item) {
            $label = trim((string) preg_replace('/[^\p{L}\p{N}\s&\-]/u', '', (string) $item['label']));

            foreach (PublicLocale::supported() as $locale) {
                $lines[] = sprintf('- [%s (%s)](%s%s)', $label, $locale, $base, PublicLocale::localizedPath((string) $item['href'], $locale));
            }
        }

        $links = (array) ($author['same_as'] ?? []);

        if ($links !== []) {
            $lines[] = '';
            $lines[] = '## Elsewhere';

            foreach ($links as $url) {
                $lines[] = "- {$url}";
            }
        }

        $lines[] = '';

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
