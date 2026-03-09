<?php

namespace App\Console\Commands;

use App\Services\ContentRepository;
use App\Support\ContentVisual;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Throwable;

class ExportStaticPreviewCommand extends Command
{
    protected $signature = 'site:export-static-preview
        {--locale=fr : Single locale to export}
        {--locales= : Comma-separated locales to export}
        {--output=dist/static-preview : Output directory, relative to the repo root}
        {--base= : Public base path used by the static host}
        {--host=127.0.0.1 : Host used by the local export server}
        {--port=8099 : Port used by the local export server}';

    protected $description = 'Export a static HTML preview of the public site for GitHub Pages or any static host.';

    /** @var array<int, string> */
    protected array $assetPaths = [];

    /** @var array<int, string> */
    protected array $exportedUrls = [];

    public function handle(ContentRepository $content): int
    {
        $locales = $this->localesToExport();
        $outputPath = base_path((string) $this->option('output'));
        $basePath = $this->normalizeBasePath((string) ($this->option('base') ?: '/'.basename(base_path()).'/'));
        $host = (string) $this->option('host');
        $port = (int) $this->option('port');
        $serverUrl = "http://{$host}:{$port}";
        $serverProcess = null;
        $ssrProcess = null;
        $hotPath = public_path('hot');
        $hotBackupPath = public_path('hot.static-preview.bak');

        try {
            $this->prepareOutputDirectory($outputPath);
            $this->disableHotFile($hotPath, $hotBackupPath);
            $this->runBuild();

            $serverProcess = $this->startProcess([
                PHP_BINARY,
                'artisan',
                'serve',
                "--host={$host}",
                "--port={$port}",
            ]);

            $ssrProcess = $this->startProcess([
                PHP_BINARY,
                'artisan',
                'inertia:start-ssr',
            ]);

            $this->waitForApplication($serverUrl);
            $this->waitForSsr();

            foreach ($locales as $locale) {
                $routes = $this->routesToExport($content, $locale);

                foreach ($routes as $route) {
                    $this->exportRoute($serverUrl, $route, $locale, $basePath, $outputPath);
                }
            }

            $this->copyStaticAssets($outputPath, $basePath);
            foreach ($locales as $locale) {
                $this->generatePlaceholderAssets($content, $outputPath, $locale);
            }
            $this->copyCareerPdf($outputPath, 'en');
            $this->copyCareerPdf($outputPath, 'fr');
            $this->generateStaticManifest($outputPath, $basePath);
            $this->generateServiceWorker($outputPath, $basePath);

            File::put($outputPath.'/.nojekyll', '');

            $indexPath = $outputPath.'/index.html';

            if (File::exists($indexPath)) {
                File::copy($indexPath, $outputPath.'/404.html');
            }

            $this->info("Static preview exported to [{$outputPath}] with base path [{$basePath}].");

            return self::SUCCESS;
        } catch (Throwable $error) {
            $this->error($error->getMessage());

            return self::FAILURE;
        } finally {
            $this->stopProcess($ssrProcess);
            $this->stopProcess($serverProcess);
            $this->restoreHotFile($hotPath, $hotBackupPath);
        }
    }

    protected function prepareOutputDirectory(string $outputPath): void
    {
        if (File::isDirectory($outputPath)) {
            File::deleteDirectory($outputPath);
        }

        File::ensureDirectoryExists($outputPath);
    }

    protected function disableHotFile(string $hotPath, string $hotBackupPath): void
    {
        if (File::exists($hotPath)) {
            File::move($hotPath, $hotBackupPath);
        }
    }

    protected function restoreHotFile(string $hotPath, string $hotBackupPath): void
    {
        if (File::exists($hotBackupPath)) {
            if (File::exists($hotPath)) {
                File::delete($hotPath);
            }

            File::move($hotBackupPath, $hotPath);
        }
    }

    protected function runBuild(): void
    {
        $this->line('Building client and SSR bundles...');
        $this->runProcess([$this->npmBinary(), 'run', 'build:ssr']);
    }

    protected function waitForApplication(string $serverUrl): void
    {
        $this->line('Waiting for the local export server...');

        $deadline = microtime(true) + 60;

        while (microtime(true) < $deadline) {
            try {
                $response = Http::timeout(2)->get($serverUrl.'/up');

                if ($response->successful()) {
                    return;
                }
            } catch (Throwable) {
                // Keep polling until the deadline.
            }

            usleep(300000);
        }

        throw new \RuntimeException('The local export server did not become ready in time.');
    }

    protected function waitForSsr(): void
    {
        $this->line('Waiting for the Inertia SSR server...');

        $deadline = microtime(true) + 60;

        while (microtime(true) < $deadline) {
            $process = new Process([PHP_BINARY, 'artisan', 'inertia:check-ssr'], base_path());
            $process->run();

            if ($process->isSuccessful()) {
                return;
            }

            usleep(300000);
        }

        throw new \RuntimeException('The Inertia SSR server did not become ready in time.');
    }

    /**
     * @return array<int, string>
     */
    protected function routesToExport(ContentRepository $content, string $locale): array
    {
        $routes = [
            '/',
            '/projects',
            '/local',
            '/journal',
            '/contact',
            '/data-processing',
            '/case-studies',
            '/labs',
        ];

        $writing = $content->published('writing', $locale, false)
            ->map(fn (array $item): string => '/journal/'.$item['slug']);

        $caseStudies = $content->published('case-studies', $locale, false)
            ->map(fn (array $item): string => '/case-studies/'.$item['slug']);

        return collect($routes)
            ->merge($writing)
            ->merge($caseStudies)
            ->unique()
            ->values()
            ->all();
    }

    protected function exportRoute(
        string $serverUrl,
        string $route,
        string $locale,
        string $basePath,
        string $outputPath,
    ): void {
        $pathWithLocale = $route === '/'
            ? $serverUrl.'/?lang='.$locale
            : $serverUrl.$route.'?lang='.$locale;

        $response = Http::timeout(15)
            ->withHeaders([
                'X-Static-Preview' => '1',
                'X-Static-Preview-Base' => $basePath,
            ])
            ->get($pathWithLocale);

        if (! $response->successful()) {
            throw new \RuntimeException("Failed to render [{$route}] for static export.");
        }

        $html = $this->rewriteHtml($response->body(), $basePath, $serverUrl, $locale);
        $targetPath = $this->targetPathForRoute($outputPath, $route, $locale);

        File::ensureDirectoryExists(dirname($targetPath));
        File::put($targetPath, $html);
        $this->exportedUrls[] = $this->publicUrlForRoute($route, $locale, $basePath);
    }

    protected function rewriteHtml(
        string $html,
        string $basePath,
        string $serverUrl,
        string $locale,
    ): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML($html, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED | LIBXML_NOERROR | LIBXML_NOWARNING);

        $xpath = new DOMXPath($dom);

        $htmlElement = $dom->documentElement;

        if ($htmlElement instanceof DOMElement) {
            $htmlElement->setAttribute('data-static-preview', '1');
        }

        foreach (['href', 'src', 'action', 'content'] as $attribute) {
            foreach ($xpath->query(sprintf('//*[@%s]', $attribute)) ?: [] as $node) {
                if (! $node instanceof DOMElement) {
                    continue;
                }

                $value = $node->getAttribute($attribute);
                $rewritten = $this->rewriteStringValue($value, $basePath, $serverUrl, $locale);

                if ($rewritten !== $value) {
                    $node->setAttribute($attribute, $rewritten);
                }
            }
        }

        foreach ($xpath->query('//*[@data-page]') ?: [] as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $page = json_decode($node->getAttribute('data-page'), true);

            if (! is_array($page)) {
                continue;
            }

            $rewrittenPage = $this->rewritePayloadValue($page, $basePath, $serverUrl, $locale);
            $node->setAttribute(
                'data-page',
                json_encode(
                    $rewrittenPage,
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ) ?: $node->getAttribute('data-page'),
            );
        }

        $head = $dom->getElementsByTagName('head')->item(0);

        if ($head instanceof DOMElement) {
            $previewMeta = $dom->createElement('meta');
            $previewMeta->setAttribute('name', 'static-preview');
            $previewMeta->setAttribute('content', '1');
            $head->appendChild($previewMeta);

            $manifestLink = $dom->createElement('link');
            $manifestLink->setAttribute('rel', 'manifest');
            $manifestLink->setAttribute('href', rtrim($basePath, '/').'/manifest.webmanifest');
            $head->appendChild($manifestLink);

            $themeColorMeta = $dom->createElement('meta');
            $themeColorMeta->setAttribute('name', 'theme-color');
            $themeColorMeta->setAttribute('content', '#e3d6c1');
            $head->appendChild($themeColorMeta);
        }

        $rendered = $dom->saveHTML();

        return $rendered === false ? $html : $rendered;
    }

    protected function rewritePayloadValue(
        mixed $value,
        string $basePath,
        string $serverUrl,
        string $locale,
    ): mixed
    {
        if (is_array($value)) {
            $rewritten = [];

            foreach ($value as $key => $item) {
                $rewritten[$key] = $this->rewritePayloadValue($item, $basePath, $serverUrl, $locale);
            }

            return $rewritten;
        }

        if (! is_string($value)) {
            return $value;
        }

        return $this->rewriteStringValue($value, $basePath, $serverUrl, $locale);
    }

    protected function rewriteStringValue(
        string $value,
        string $basePath,
        string $serverUrl,
        string $locale,
    ): string
    {
        if ($value === '') {
            return $value;
        }

        if (Str::contains($value, ['href="/', "href='/", 'src="/', "src='/"])) {
            return (string) preg_replace_callback(
                '/(?<prefix>(?:href|src)=["\'])(?<path>\/[^"\']*)(?<suffix>["\'])/',
                fn (array $matches): string => $matches['prefix']
                    .$this->rewritePath($matches['path'], $basePath, $locale)
                    .$matches['suffix'],
                $value,
            );
        }

        if (Str::startsWith($value, 'http://') || Str::startsWith($value, 'https://')) {
            if (Str::startsWith($value, $serverUrl)) {
                $parsed = parse_url($value);
                $path = $parsed['path'] ?? '/';
                $query = isset($parsed['query']) ? '?'.$parsed['query'] : '';
                $fragment = isset($parsed['fragment']) ? '#'.$parsed['fragment'] : '';

                return $this->rewritePath(
                    $path.$query.$fragment,
                    $basePath,
                    $locale,
                );
            }

            return $value;
        }

        if (! Str::startsWith($value, '/')) {
            return $value;
        }

        return $this->rewritePath($value, $basePath, $locale);
    }

    protected function rewritePath(string $path, string $basePath, string $locale): string
    {
        if ($path === '' || Str::startsWith($path, ['mailto:', 'tel:', '#', 'data:', 'javascript:'])) {
            return $path;
        }

        if (Str::startsWith($path, $basePath)) {
            return $path;
        }

        [$cleanPath, $suffix] = $this->splitPathSuffix($path);
        [$targetLocale, $suffix] = $this->extractTargetLocale($suffix, $locale);
        $localePrefix = $this->localePrefix($targetLocale);

        if ($cleanPath === '/cv/en') {
            return $basePath.'assets/cv/ismael-rodmacq-cv-en.pdf'.$suffix;
        }

        if ($cleanPath === '/cv/fr') {
            return $basePath.'assets/cv/ismael-rodmacq-cv-fr.pdf'.$suffix;
        }

        if ($this->isAssetPath($cleanPath)) {
            $this->assetPaths[] = $cleanPath;

            return rtrim($basePath, '/').$cleanPath.$suffix;
        }

        if ($cleanPath === '/') {
            return rtrim($basePath, '/').$localePrefix.'/'.$suffix;
        }

        return rtrim($basePath, '/').$localePrefix.rtrim($cleanPath, '/').'/'.$suffix;
    }

    protected function isAssetPath(string $path): bool
    {
        return Str::startsWith($path, [
            '/build/',
            '/images/',
            '/uploads/branding/',
            '/content-visuals/',
        ]) || in_array($path, [
            '/favicon.ico',
            '/favicon.svg',
            '/apple-touch-icon.png',
            '/robots.txt',
            '/sitemap.xml',
        ], true);
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function splitPathSuffix(string $path): array
    {
        $query = '';
        $hash = '';

        if (str_contains($path, '#')) {
            [$path, $hash] = explode('#', $path, 2);
            $hash = '#'.$hash;
        }

        if (str_contains($path, '?')) {
            [$path, $query] = explode('?', $path, 2);
            $query = '?'.$query;
        }

        return [$path, $query.$hash];
    }

    protected function targetPathForRoute(string $outputPath, string $route, string $locale): string
    {
        $prefix = ltrim($this->localePrefix($locale), '/');

        if ($route === '/') {
            return $prefix === ''
                ? $outputPath.'/index.html'
                : $outputPath.'/'.$prefix.'/index.html';
        }

        $relativePath = trim($route, '/');

        return $prefix === ''
            ? $outputPath.'/'.$relativePath.'/index.html'
            : $outputPath.'/'.$prefix.'/'.$relativePath.'/index.html';
    }

    protected function copyStaticAssets(string $outputPath, string $basePath): void
    {
        if (File::isDirectory(public_path('build'))) {
            File::copyDirectory(public_path('build'), $outputPath.'/build');
            $this->rewriteBuiltAssetPaths($outputPath.'/build', $basePath);
        }

        if (File::isDirectory(public_path('images'))) {
            File::copyDirectory(public_path('images'), $outputPath.'/images');
        }

        if (File::isDirectory(public_path('uploads/branding'))) {
            File::copyDirectory(public_path('uploads/branding'), $outputPath.'/uploads/branding');
        }

        foreach (['favicon.ico', 'favicon.svg', 'apple-touch-icon.png'] as $file) {
            $source = public_path($file);

            if (File::exists($source)) {
                File::copy($source, $outputPath.'/'.$file);
            }
        }
    }

    protected function rewriteBuiltAssetPaths(string $buildPath, string $basePath): void
    {
        if (! File::isDirectory($buildPath)) {
            return;
        }

        $prefixes = [
            '/build/' => $basePath.'build/',
            '/images/' => $basePath.'images/',
            '/uploads/branding/' => $basePath.'uploads/branding/',
            '/content-visuals/' => $basePath.'content-visuals/',
            '/favicon.ico' => $basePath.'favicon.ico',
            '/favicon.svg' => $basePath.'favicon.svg',
            '/apple-touch-icon.png' => $basePath.'apple-touch-icon.png',
        ];

        foreach (File::allFiles($buildPath) as $file) {
            $extension = strtolower($file->getExtension());

            if (! in_array($extension, ['css', 'js', 'json', 'map'], true)) {
                continue;
            }

            $contents = File::get($file->getPathname());
            $rewritten = str_replace(
                array_keys($prefixes),
                array_values($prefixes),
                $contents,
            );

            if ($rewritten !== $contents) {
                File::put($file->getPathname(), $rewritten);
            }
        }
    }

    protected function generatePlaceholderAssets(
        ContentRepository $content,
        string $outputPath,
        string $locale,
    ): void {
        foreach (['writing', 'case-studies'] as $section) {
            foreach ($content->published($section, $locale, false) as $item) {
                if (($item['image']['kind'] ?? null) !== 'placeholder') {
                    continue;
                }

                $targetPath = $outputPath."/content-visuals/{$item['section']}/{$item['slug']}.svg";
                File::ensureDirectoryExists(dirname($targetPath));
                File::put($targetPath, ContentVisual::placeholderSvg($item));
            }
        }
    }

    protected function copyCareerPdf(string $outputPath, string $locale): void
    {
        $source = base_path("docs/career/output/ismael-rodmacq-cv-{$locale}.pdf");

        if (! File::exists($source)) {
            return;
        }

        $targetDirectory = $outputPath.'/assets/cv';

        File::ensureDirectoryExists($targetDirectory);
        File::copy($source, $targetDirectory."/ismael-rodmacq-cv-{$locale}.pdf");
    }

    protected function generateStaticManifest(string $outputPath, string $basePath): void
    {
        $manifest = [
            'name' => 'Ismael Rodmacq',
            'short_name' => 'Rodmacq',
            'description' => 'Static preview for full-stack e-commerce, product data, technical SEO, and editorial work.',
            'start_url' => rtrim($basePath, '/').'/',
            'scope' => $basePath,
            'display' => 'standalone',
            'background_color' => '#f4ede2',
            'theme_color' => '#e3d6c1',
            'icons' => [
                [
                    'src' => rtrim($basePath, '/').'/favicon.svg',
                    'sizes' => 'any',
                    'type' => 'image/svg+xml',
                    'purpose' => 'any',
                ],
                [
                    'src' => rtrim($basePath, '/').'/apple-touch-icon.png',
                    'sizes' => '180x180',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
            ],
        ];

        File::put(
            $outputPath.'/manifest.webmanifest',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL,
        );
    }

    protected function generateServiceWorker(string $outputPath, string $basePath): void
    {
        $base = rtrim($basePath, '/').'/';
        $precacheUrls = collect($this->exportedUrls)
            ->merge([
                $base,
                $base.'manifest.webmanifest',
                $base.'favicon.ico',
                $base.'favicon.svg',
                $base.'apple-touch-icon.png',
            ])
            ->merge($this->discoverStaticAssetUrls($outputPath, $basePath))
            ->unique()
            ->values()
            ->all();

        $version = Str::slug(now()->format('Ymd-His').'-'.substr(md5(json_encode($precacheUrls)), 0, 8));
        $precacheJson = json_encode($precacheUrls, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $serviceWorker = <<<'JS'
const BASE_PATH = __BASE_PATH__;
const VERSION = __VERSION__;
const STATIC_CACHE = `sidewalk-static-${VERSION}`;
const RUNTIME_CACHE = `sidewalk-runtime-${VERSION}`;
const PRECACHE_URLS = __PRECACHE_URLS__;

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => cache.addAll(PRECACHE_URLS)),
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys.map((key) => {
                    if (key === STATIC_CACHE || key === RUNTIME_CACHE) {
                        return Promise.resolve();
                    }

                    return caches.delete(key);
                }),
            ),
        ),
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(networkFirst(request));
        return;
    }

    if (
        url.pathname.startsWith(`${BASE_PATH}build/`) ||
        url.pathname.startsWith(`${BASE_PATH}images/`) ||
        url.pathname.startsWith(`${BASE_PATH}content-visuals/`) ||
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'font' ||
        request.destination === 'image'
    ) {
        event.respondWith(cacheFirst(request));
    }
});

async function networkFirst(request) {
    const cache = await caches.open(RUNTIME_CACHE);

    try {
        const response = await fetch(request);
        cache.put(request, response.clone());
        return response;
    } catch {
        const cached = await cache.match(request);

        if (cached) {
            return cached;
        }

        return (await caches.match(`${BASE_PATH}`)) || Response.error();
    }
}

async function cacheFirst(request) {
    const cached = await caches.match(request);

    if (cached) {
        return cached;
    }

    const response = await fetch(request);
    const cache = await caches.open(RUNTIME_CACHE);
    cache.put(request, response.clone());
    return response;
}
JS;

        $serviceWorker = str_replace(
            ['__BASE_PATH__', '__VERSION__', '__PRECACHE_URLS__'],
            [
                $this->jsonEncodeForJs($base),
                $this->jsonEncodeForJs($version),
                $precacheJson,
            ],
            $serviceWorker,
        );

        File::put($outputPath.'/sw.js', $serviceWorker.PHP_EOL);
    }

    protected function normalizeBasePath(string $basePath): string
    {
        $trimmed = trim($basePath);

        if ($trimmed === '' || $trimmed === '/') {
            return '/';
        }

        return '/'.trim($trimmed, '/').'/';
    }

    /**
     * @return array<int, string>
     */
    protected function localesToExport(): array
    {
        $localesOption = trim((string) $this->option('locales'));

        if ($localesOption === '') {
            return [(string) $this->option('locale')];
        }

        return collect(explode(',', $localesOption))
            ->map(fn (string $locale): string => trim($locale))
            ->filter(fn (string $locale): bool => $locale !== '')
            ->unique()
            ->values()
            ->all();
    }

    protected function localePrefix(string $locale): string
    {
        return $locale === 'fr' ? '' : '/'.$locale;
    }

    protected function publicUrlForRoute(string $route, string $locale, string $basePath): string
    {
        $prefix = $this->localePrefix($locale);
        $normalizedBase = rtrim($basePath, '/');

        if ($route === '/') {
            return $normalizedBase.($prefix !== '' ? $prefix : '').'/';
        }

        return $normalizedBase.($prefix !== '' ? $prefix : '').$route.'/';
    }

    /**
     * @return array<int, string>
     */
    protected function discoverStaticAssetUrls(string $outputPath, string $basePath): array
    {
        $base = rtrim($basePath, '/');

        return collect(File::allFiles($outputPath))
            ->map(function ($file) use ($outputPath, $base): ?string {
                $relativePath = str_replace('\\', '/', Str::after($file->getPathname(), $outputPath.DIRECTORY_SEPARATOR));

                if (
                    $relativePath === '' ||
                    str_ends_with($relativePath, '.html') ||
                    $relativePath === '.nojekyll'
                ) {
                    return null;
                }

                return $base.'/'.$relativePath;
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function jsonEncodeForJs(string $value): string
    {
        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '""';
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function extractTargetLocale(string $suffix, string $fallbackLocale): array
    {
        if ($suffix === '' || ! str_contains($suffix, '?')) {
            return [$fallbackLocale, $suffix];
        }

        $hash = '';
        $query = $suffix;

        if (str_contains($suffix, '#')) {
            [$query, $hashPart] = explode('#', $suffix, 2);
            $hash = '#'.$hashPart;
        }

        parse_str(ltrim($query, '?'), $parameters);

        $targetLocale = $fallbackLocale;

        if (
            isset($parameters['lang']) &&
            in_array($parameters['lang'], ['fr', 'en'], true)
        ) {
            $targetLocale = $parameters['lang'];
        }

        unset($parameters['lang']);

        $rebuiltQuery = http_build_query($parameters);

        return [
            $targetLocale,
            ($rebuiltQuery !== '' ? '?'.$rebuiltQuery : '').$hash,
        ];
    }

    protected function npmBinary(): string
    {
        return PHP_OS_FAMILY === 'Windows' ? 'npm.cmd' : 'npm';
    }

    /**
     * @param  array<int, string>  $command
     */
    protected function runProcess(array $command): void
    {
        $process = new Process($command, base_path());
        $process->setTimeout(null);
        $process->run(function (string $type, string $buffer): void {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            throw new \RuntimeException(trim($process->getErrorOutput()) ?: 'A child process failed.');
        }
    }

    /**
     * @param  array<int, string>  $command
     */
    protected function startProcess(array $command): Process
    {
        $process = new Process($command, base_path());
        $process->setTimeout(null);
        $process->start();

        return $process;
    }

    protected function stopProcess(?Process $process): void
    {
        if (! $process instanceof Process) {
            return;
        }

        if ($process->isRunning()) {
            $process->stop(3);
        }
    }
}
