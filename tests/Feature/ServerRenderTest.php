<?php

namespace Tests\Feature;

use App\Support\Ssr\TimedHttpGateway;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Inertia\Ssr\HttpGateway;
use Tests\TestCase;

/**
 * The server render is an extra, never a dependency.
 *
 * Production sends each page to a Node function that renders it, so the HTML a
 * crawler fetches already holds the headings and the text. Everything about it
 * is built so that its absence changes nothing: a function that fails, is slow
 * or is not there leaves the page to the browser, as before.
 */
class ServerRenderTest extends TestCase
{
    private const PAGE = ['component' => 'Home', 'props' => [], 'url' => '/en', 'version' => 'x'];

    protected function setUp(): void
    {
        parent::setUp();

        config(['inertia.ssr.enabled' => true, 'inertia.ssr.ensure_bundle_exists' => false]);
    }

    public function test_a_rendered_page_gives_its_body_and_leaves_the_head_to_the_shell(): void
    {
        Http::fake(['*' => Http::response(['head' => ['<title>x</title>', '<meta name="a">'], 'body' => '<div id="app"><h1>Hello</h1></div>'])]);

        $response = (new TimedHttpGateway)->dispatch(self::PAGE);

        $this->assertNotNull($response);
        // The head of the document is written once, by the Blade shell: the render server's copy would double every tag.
        $this->assertSame('', $response->head);
        $this->assertStringContainsString('<h1>Hello</h1>', $response->body);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/render'));
    }

    public function test_a_failing_function_leaves_the_page_to_the_browser(): void
    {
        Http::fake(['*' => Http::response(['error' => 'boom', 'type' => 'render'], 500)]);

        $this->assertNull((new TimedHttpGateway)->dispatch(self::PAGE));
    }

    public function test_a_function_that_cannot_be_reached_leaves_the_page_to_the_browser(): void
    {
        Http::fake(fn () => throw new ConnectionException('timed out'));

        $this->assertNull((new TimedHttpGateway)->dispatch(self::PAGE));
    }

    public function test_the_render_has_a_short_fuse(): void
    {
        $this->assertLessThanOrEqual(8, TimedHttpGateway::TIMEOUT);
        $this->assertLessThanOrEqual(TimedHttpGateway::TIMEOUT, TimedHttpGateway::CONNECT_TIMEOUT);
    }

    public function test_the_app_uses_the_timed_gateway(): void
    {
        $this->assertInstanceOf(TimedHttpGateway::class, app(HttpGateway::class));
    }

    public function test_on_vercel_the_render_server_is_the_function_of_the_same_deployment(): void
    {
        $config = (string) file_get_contents(config_path('inertia.php'));

        $this->assertStringContainsString("env('VERCEL')", $config);
        $this->assertStringContainsString("'/api/ssr'", $config);

        $vercel = json_decode((string) file_get_contents(base_path('vercel.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('api/ssr/render.mjs', $vercel['functions']);
        $this->assertStringContainsString('bootstrap/ssr-render', $vercel['functions']['api/ssr/render.mjs']['includeFiles']);
        $this->assertFileExists(base_path('api/ssr/render.mjs'));
    }

    public function test_the_build_makes_the_bundle_of_the_function(): void
    {
        $package = json_decode((string) file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertStringContainsString('--ssr resources/js/ssr-render.ts', $package['scripts']['build']);
        $this->assertStringContainsString('--outDir bootstrap/ssr-render', $package['scripts']['build']);
    }
}
