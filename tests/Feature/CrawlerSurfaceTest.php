<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * What a crawler, a scanner or a mistyped address meets.
 *
 * - the files a crawler fetches set no cookie and need no session;
 * - an address that does not exist answers 404 with the site's own page,
 *   never indexed;
 * - every page carries a Person node, and the site name is in a title once;
 * - the five security headers are declared for every response, and the CSP
 *   allows what the site loads and nothing else;
 * - the head tags Inertia replaces are marked, so they are never doubled.
 */
class CrawlerSurfaceTest extends TestCase
{
    public function test_sitemap_robots_and_llms_set_no_cookie(): void
    {
        foreach (['/sitemap.xml', '/robots.txt', '/llms.txt'] as $path) {
            $response = $this->get($path)->assertOk();

            $this->assertEmpty(
                $response->headers->getCookies(),
                "{$path} must not set a cookie.",
            );
        }
    }

    public function test_llms_txt_reads_as_the_site_and_links_every_locale(): void
    {
        $body = $this->get('/llms.txt')->assertOk()->getContent();

        $this->assertStringStartsWith('# ', $body);
        $this->assertStringContainsString('## Pages', $body);
        $this->assertStringContainsString('/en/work', $body);
        $this->assertStringContainsString('/fr/work', $body);
    }

    public function test_robots_keeps_crawlers_out_of_the_back_office(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap:');
    }

    public function test_an_unknown_address_answers_404_with_the_site_page(): void
    {
        foreach (['/en/nothing-here', '/fr/rien-ici', '/zzz'] as $path) {
            $this->get($path)
                ->assertNotFound()
                ->assertSee('noindex', false);
        }

        $this->get('/en/nothing-here')->assertSee('Page not found');
        $this->get('/fr/rien-ici')->assertSee('Page introuvable');
    }

    public function test_an_unknown_back_office_address_keeps_the_default_404(): void
    {
        $this->get('/admin/nothing-here')->assertStatus(404)->assertDontSee('Page not found');
    }

    public function test_every_page_names_the_person_behind_the_site(): void
    {
        foreach (['/en', '/en/work', '/en/services', '/fr/experience'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            $this->assertMatchesRegularExpression(
                '/"@type":"Person"[^}]*"@id":"[^"]+#person"|"@id":"[^"]+#person"[^}]*"@type":"Person"/',
                $html,
                "{$path} should carry a Person node.",
            );
            $this->assertStringContainsString('"@type":"WebSite"', $html);
        }
    }

    public function test_the_home_title_names_the_site_once(): void
    {
        $title = $this->titleOf($this->get('/en')->assertOk()->getContent());

        $this->assertSame(1, substr_count(mb_strtolower($title), 'rodmacq'), $title);
        $this->assertLessThanOrEqual(62, mb_strlen($title), $title);
    }

    public function test_the_head_tags_inertia_replaces_are_marked_so_they_are_not_doubled(): void
    {
        $html = $this->get('/en/work')->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('/<link data-inertia rel="canonical"/', $html));
        $this->assertSame(0, preg_match_all('/<link rel="canonical"/', $html));
        $this->assertStringContainsString('<meta data-inertia name="description"', $html);
    }

    public function test_the_five_security_headers_are_declared_for_every_response(): void
    {
        $config = json_decode((string) file_get_contents(base_path('vercel.json')), true, flags: JSON_THROW_ON_ERROR);
        $rule = collect($config['headers'])->firstWhere('source', '/(.*)');

        $this->assertNotNull($rule, 'vercel.json declares headers for every path.');

        $headers = collect($rule['headers'])->pluck('value', 'key');

        foreach (['Content-Security-Policy', 'X-Frame-Options', 'X-Content-Type-Options', 'Referrer-Policy', 'Permissions-Policy'] as $key) {
            $this->assertTrue($headers->has($key), "{$key} is declared.");
        }

        $this->assertSame('nosniff', $headers['X-Content-Type-Options']);
        $this->assertSame('SAMEORIGIN', $headers['X-Frame-Options']);

        $csp = (string) $headers['Content-Security-Policy'];

        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        // The speculation rules in the document are inline script; without this keyword the browser ignores them.
        $this->assertStringContainsString("'inline-speculation-rules'", $csp);
    }

    public function test_the_php_version_is_not_advertised(): void
    {
        $entry = (string) file_get_contents(base_path('api/index.php'));

        $this->assertStringContainsString("header_remove('X-Powered-By')", $entry);
    }

    private function titleOf(string $html): string
    {
        preg_match('/<title[^>]*>(.*?)<\/title>/s', $html, $m);

        return html_entity_decode(trim($m[1] ?? ''), ENT_QUOTES);
    }
}
