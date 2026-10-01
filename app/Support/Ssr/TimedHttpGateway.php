<?php

namespace App\Support\Ssr;

use Exception;
use Illuminate\Http\Client\StrayRequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Ssr\HttpGateway;
use Inertia\Ssr\Response;
use Inertia\Ssr\SsrException;

/**
 * The Inertia HTTP gateway with a short fuse.
 *
 * The stock gateway posts the page to the render server with the HTTP client's
 * default timeout of thirty seconds. Here the render server is a serverless
 * function that may be cold, and a slow one must never hold a page hostage: after
 * a few seconds the request gives up and the page is sent for the browser to
 * render, exactly as it was before any server render existed. A failed render is
 * a slower page for a crawler, never a broken one.
 *
 * It also keeps the document head out of the render: the head of a page is
 * written once, by the Blade shell.
 */
class TimedHttpGateway extends HttpGateway
{
    /** Seconds to wait for the whole render, and for the connection alone. */
    public const TIMEOUT = 5;

    public const CONNECT_TIMEOUT = 2;

    /**
     * @param  array<string, mixed>  $page
     */
    public function dispatch(array $page, ?Request $request = null): ?Response
    {
        if (! $this->ssrIsEnabled($request ?? request())) {
            return null;
        }

        if ($this->shouldEnsureBundleExists() && ! $this->bundleExists()) {
            return null;
        }

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->connectTimeout(self::CONNECT_TIMEOUT)
                ->post($this->getProductionUrl('/render'), $page);

            if ($response->failed()) {
                $this->handleSsrFailure($page, $response->json());

                return null;
            }

            if (! $data = $response->json()) {
                return null;
            }

            // Only the body is taken. The document head is already complete in app.blade.php (title, description, canonical, Open Graph, JSON-LD),
            // and the render server's copy of it would double every tag: its JSON-LD also comes out as an attribute instead of as text.
            return new Response('', $data['body'] ?? '');
        } catch (Exception $e) {
            if ($e instanceof StrayRequestException || $e instanceof SsrException) {
                throw $e;
            }

            $this->handleSsrFailure($page, [
                'error' => $e->getMessage(),
                'type' => 'connection',
            ]);

            return null;
        }
    }
}
