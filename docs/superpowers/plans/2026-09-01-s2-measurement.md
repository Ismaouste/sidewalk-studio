# S2 Measurement Implementation Plan

> **Status: EXECUTED 2026-09-01** — all six tasks shipped to main, full baseline green (174 tests / 1817 assertions, lint, format, types, Pint, build), both themes visually verified on /data-processing, T1 loop verified end to end locally (browser ping → 204 → structured stderr log line, no IP). Two corrections found while executing: Laravel 13's web group registers `PreventRequestForgery` (not its `ValidateCsrfToken` alias), so the stateless route must exclude that class; and the V1 sentinel needed a block wrapper because a bare child of the article panel's grid costs one row gap. PostHog activation awaits env vars on Vercel (see `docs/architecture/measurement.md` runbook).

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ship consent tiers T1–T3 — a first-party CNIL-exemptable audience ping, PostHog EU Cloud behind the `analytics` consent category, session replay behind its own explicit switch — plus the V0–V4 funnel-stage events.

**Architecture:** T1 is a stateless Laravel POST endpoint (no session, no cookie, no CSRF) that normalizes a page-view ping (locale-stripped path, referrer host only, truncated IP folded into a daily-rotating HMAC digest) and hands it to a pluggable sink — structured log line by default (prod has no database; Vercel drains function logs), server-side PostHog capture once a key exists. T2 loads `posthog-js` by dynamic import only when the `analytics` category is accepted, replacing the CustomEvent placeholder in the consent registry with a direct import (no event-ordering risk). T3 is a localStorage-backed opt-in surfaced on `/data-processing`, never part of "Accept all". Funnel events ride a tiny `capture()` facade that no-ops until PostHog is live.

**Tech Stack:** Laravel 13, Inertia 3, Vue 3, `vanilla-cookieconsent` (already present), `posthog-js` (new, lazy-loaded), PHPUnit 13.

**Spec:** `docs/superpowers/specs/2026-09-01-commercial-repositioning-design.md` (§3 funnel stages, §6 measurement and consent tiers, §11 S2 scope, §12 resolved decisions)

## Global Constraints

- Production (Vercel) ships **no database**: nothing here may require a DB write. Prod filesystem is read-only outside `/tmp`; the default T1 sink logs to the app logger (drained by Vercel), never to a file path of its own.
- All code, docs, and content keys English-only; public copy bilingual FR/EN in shape parity (`DeclaredPageContentTest` + `LanguageFileParityTest` enforce it).
- Never hardcode colors/fonts/spacing/motion — only `--sw-*` tokens. Test `morning` and `sunset` themes on visual changes; sunset carries no green and no amber.
- TS copy modules: FR ends with `satisfies typeof import('../../en/...')`, keys sorted (`sort-keys` lint rule).
- UI copy lives in `resources/js/copy/<locale>/…`, never inline in components.
- Commits go straight to `main`; push only after the full baseline (Task 6) because push deploys to Vercel.
- Platform primitives over components: IntersectionObserver, native checkbox switches, window events from vanilla-cookieconsent (`cc:onConsent`, `cc:onChange`) — no polling, no new store.
- `posthog-js` must never enter the main bundle: dynamic `import()` only, behind accepted consent.
- Everything must degrade to a working site with **zero env vars set**: `ANALYTICS_DRIVER=none` keeps T2/T3 dormant, `AUDIENCE_SINK=log` keeps T1 self-contained. PostHog activation is an env change on Vercel (`ANALYTICS_DRIVER=posthog`, `POSTHOG_KEY`, optionally `AUDIENCE_SINK=posthog`), not a deploy.
- The PostHog project API key is a public, write-only key by design; sharing it in Inertia props is correct. The raw client IP and full user agent must never reach a sink or a log line.
- Formatting: the Prettier hook fires on Edit/Write tool use only; when files are produced via shell, run `npx prettier --write <files>` before committing. PHP is formatted by Pint (`composer run lint`).

---

### Task 1: T1 backend — config, sinks, ping controller, stateless route

**Files:**

- Create: `config/audience.php`
- Create: `app/Audience/AudienceSink.php`, `app/Audience/LogAudienceSink.php`, `app/Audience/PostHogAudienceSink.php`
- Create: `app/Http/Controllers/AudiencePingController.php`
- Modify: `app/Providers/AppServiceProvider.php` (sink binding)
- Modify: `routes/web.php` (stateless route, above the locale group)
- Modify: `config/consent.php` (posthog service block; refreshed analytics description)
- Modify: `.env.example`
- Test: create `tests/Feature/AudiencePingTest.php`

**Interfaces:**

- Produces: `POST /audience` (route name `audience.ping`) accepting JSON `{path: string, locale: 'en'|'fr', referrer?: string|null}` → 204, no `Set-Cookie`. Honors `Sec-GPC: 1` and `config('audience.enabled')` by discarding silently (still 204). Invalid payload → 422 JSON, never a redirect (no session exists on this route).
- Produces: `App\Audience\AudienceSink` interface with `record(array $event): void`; event shape consumed by every sink and by Task 2's tests:

```php
[
    'date' => '2026-09-01',            // Y-m-d, server clock
    'path' => '/services',             // locale prefix stripped, '/' for home
    'locale' => 'en',
    'referrer_host' => 'duckduckgo.com', // external hosts only, else null
    'device' => 'mobile',              // 'mobile' | 'desktop', from a single UA substring
    'visitor' => 'a1b2c3d4e5f60718',   // 16 hex chars, daily-rotating HMAC, never reversible to an IP
    'occurred_at' => '2026-09-01T12:00:00+00:00',
]
```

- Produces: `config('consent.services.analytics.posthog')` = `['key' => env('POSTHOG_KEY'), 'host' => env('POSTHOG_HOST', 'https://eu.i.posthog.com')]` — the single home for PostHog credentials; Task 3's client reads it through the existing consent share, this task's server sink reads it directly.

- [ ] **Step 1: Write the failing tests** — `tests/Feature/AudiencePingTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Audience\AudienceSink;
use App\Audience\LogAudienceSink;
use App\Audience\PostHogAudienceSink;
use ArrayObject;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AudiencePingTest extends TestCase
{
    private ArrayObject $events;

    protected function setUp(): void
    {
        parent::setUp();

        $this->events = new ArrayObject();
        $events = $this->events;

        $this->app->instance(AudienceSink::class, new class($events) implements AudienceSink
        {
            public function __construct(private ArrayObject $events)
            {
            }

            public function record(array $event): void
            {
                $this->events->append($event);
            }
        });
    }

    public function test_a_ping_is_accepted_without_setting_any_cookie(): void
    {
        $response = $this->postJson('/audience', [
            'path' => '/en/services',
            'locale' => 'en',
            'referrer' => 'https://duckduckgo.com/some/path',
        ]);

        $response->assertNoContent();
        $this->assertNull($response->headers->get('Set-Cookie'));

        $this->assertCount(1, $this->events);
        $event = $this->events[0];

        $this->assertSame('/services', $event['path']);
        $this->assertSame('en', $event['locale']);
        $this->assertSame('duckduckgo.com', $event['referrer_host']);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{16}$/', $event['visitor']);
        $this->assertArrayNotHasKey('ip', $event);
        $this->assertStringNotContainsString('127.0.0.1', json_encode($event));
    }

    public function test_the_home_path_keeps_a_slash_and_own_referrers_are_dropped(): void
    {
        $this->postJson('/audience', [
            'path' => '/fr',
            'locale' => 'fr',
            'referrer' => rtrim((string) config('site.url'), '/').'/en/journal',
        ])->assertNoContent();

        $this->assertSame('/', $this->events[0]['path']);
        $this->assertNull($this->events[0]['referrer_host']);
    }

    public function test_two_visitors_on_the_same_network_share_a_daily_digest(): void
    {
        $payload = json_encode(['path' => '/en', 'locale' => 'en']);
        $server = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Linux x86_64)',
        ];

        $this->call('POST', '/audience', [], [], [], $server + ['REMOTE_ADDR' => '203.0.113.5'], $payload);
        $this->call('POST', '/audience', [], [], [], $server + ['REMOTE_ADDR' => '203.0.113.99'], $payload);
        $this->call('POST', '/audience', [], [], [], $server + [
            'REMOTE_ADDR' => '203.0.113.5',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_0 like Mac OS X) Mobile/15E148',
        ], $payload);

        $this->assertCount(3, $this->events);
        $this->assertSame($this->events[0]['visitor'], $this->events[1]['visitor']);
        $this->assertNotSame($this->events[0]['visitor'], $this->events[2]['visitor']);
        $this->assertSame('desktop', $this->events[0]['device']);
        $this->assertSame('mobile', $this->events[2]['device']);
    }

    public function test_global_privacy_control_is_honored_server_side(): void
    {
        $this->postJson('/audience', ['path' => '/en', 'locale' => 'en'], ['Sec-GPC' => '1'])
            ->assertNoContent();

        $this->assertCount(0, $this->events);
    }

    public function test_a_disabled_config_discards_without_erroring(): void
    {
        config(['audience.enabled' => false]);

        $this->postJson('/audience', ['path' => '/en', 'locale' => 'en'])->assertNoContent();

        $this->assertCount(0, $this->events);
    }

    public function test_a_malformed_ping_is_refused_with_json_even_without_an_accept_header(): void
    {
        $this->postJson('/audience', ['path' => 'not-a-path', 'locale' => 'de'])->assertStatus(422);

        $this->call('POST', '/audience', [], [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode(['path' => 'bad', 'locale' => 'de']))->assertStatus(422);

        $this->assertCount(0, $this->events);
    }

    public function test_the_log_sink_writes_one_structured_line(): void
    {
        Log::spy();

        (new LogAudienceSink())->record(['path' => '/services', 'visitor' => 'abc']);

        Log::shouldHaveReceived('info')->once()->with('audience.ping', ['path' => '/services', 'visitor' => 'abc']);
    }

    public function test_the_posthog_sink_captures_server_side_and_stays_quiet_without_a_key(): void
    {
        Http::fake();

        $event = [
            'date' => '2026-09-01',
            'path' => '/services',
            'locale' => 'en',
            'referrer_host' => null,
            'device' => 'desktop',
            'visitor' => 'abc',
            'occurred_at' => '2026-09-01T12:00:00+00:00',
        ];

        config(['consent.services.analytics.posthog.key' => null]);
        (new PostHogAudienceSink())->record($event);
        Http::assertNothingSent();

        config(['consent.services.analytics.posthog.key' => 'phc_test']);
        (new PostHogAudienceSink())->record($event);

        Http::assertSent(function ($request): bool {
            return str_starts_with($request->url(), 'https://eu.i.posthog.com/')
                && $request['api_key'] === 'phc_test'
                && $request['event'] === 'audience_ping'
                && $request['distinct_id'] === 'abc'
                && $request['properties']['$process_person_profile'] === false;
        });
    }
}
```

- [ ] **Step 2: Run it** — `php artisan test --filter=AudiencePingTest` → FAIL (class not found / 404).

- [ ] **Step 3: Config.** Create `config/audience.php`:

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | First-party audience ping (consent tier T1)
    |--------------------------------------------------------------------------
    |
    | A cookieless page-count ping designed against the CNIL audience-
    | measurement exemption criteria: no cross-site identifier, no cookie,
    | truncated IP folded into a digest that rotates daily, and a client-side
    | opt-out plus server-side Global Privacy Control handling. It runs even
    | at 0% consent because it does not need any.
    |
    */

    'enabled' => (bool) env('AUDIENCE_ENABLED', true),

    // 'log' writes one structured line per ping to the app logger (Vercel
    // drains function logs; prod has no database). 'posthog' relays the
    // anonymous event server-side to the EU project configured in
    // config/consent.php.
    'sink' => env('AUDIENCE_SINK', 'log'),

    'endpoint' => '/audience',
];
```

In `config/consent.php`, replace the `services.analytics` block and refresh the analytics category description (it still says "once the dedicated spec lands"):

```php
        [
            'key' => 'analytics',
            'label' => 'Analytics',
            'description' => 'Product analytics via PostHog EU Cloud, loaded only after opt-in. Session replay and heatmaps stay behind their own explicit switch on the data-processing page.',
            'readonly' => false,
            'enabled' => false,
        ],
```

```php
        'analytics' => [
            'driver' => env('ANALYTICS_DRIVER', 'none'),
            'posthog' => [
                'key' => env('POSTHOG_KEY'),
                'host' => env('POSTHOG_HOST', 'https://eu.i.posthog.com'),
            ],
        ],
```

`.env.example`, after the `CONSENT_COOKIE_NAME` line:

```
POSTHOG_KEY=
POSTHOG_HOST=https://eu.i.posthog.com
AUDIENCE_ENABLED=true
AUDIENCE_SINK=log
```

- [ ] **Step 4: Sinks.** `app/Audience/AudienceSink.php`:

```php
<?php

namespace App\Audience;

interface AudienceSink
{
    /**
     * @param  array<string, string|null>  $event
     */
    public function record(array $event): void;
}
```

`app/Audience/LogAudienceSink.php`:

```php
<?php

namespace App\Audience;

use Illuminate\Support\Facades\Log;

final class LogAudienceSink implements AudienceSink
{
    public function record(array $event): void
    {
        Log::info('audience.ping', $event);
    }
}
```

`app/Audience/PostHogAudienceSink.php`:

```php
<?php

namespace App\Audience;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class PostHogAudienceSink implements AudienceSink
{
    public function record(array $event): void
    {
        $key = config('consent.services.analytics.posthog.key');

        if (! is_string($key) || $key === '') {
            return;
        }

        $host = rtrim((string) config('consent.services.analytics.posthog.host'), '/');

        try {
            Http::timeout(2)->post($host.'/i/v0/e/', [
                'api_key' => $key,
                'event' => 'audience_ping',
                'distinct_id' => $event['visitor'],
                'timestamp' => $event['occurred_at'],
                'properties' => [
                    // Anonymous event: no person profile is ever created.
                    '$process_person_profile' => false,
                    'path' => $event['path'],
                    'locale' => $event['locale'],
                    'referrer_host' => $event['referrer_host'],
                    'device' => $event['device'],
                ],
            ]);
        } catch (Throwable $exception) {
            // A lost ping is a rounding error; a 500 on the ping route would
            // surface in every visitor's console.
            Log::debug('audience.sink_failed', ['reason' => $exception->getMessage()]);
        }
    }
}
```

In `app/Providers/AppServiceProvider.php` `register()` (with proper `use` imports at the top of the file):

```php
$this->app->bind(AudienceSink::class, fn (): AudienceSink => match (config('audience.sink')) {
    'posthog' => new PostHogAudienceSink(),
    default => new LogAudienceSink(),
});
```

- [ ] **Step 5: Controller.** `app/Http/Controllers/AudiencePingController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Audience\AudienceSink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Consent tier T1: a first-party, cookieless page-count ping.
 *
 * Designed against the CNIL audience-measurement exemption criteria: the
 * route carries no session and sets no cookie, the client IP is truncated
 * before use and only survives inside an HMAC digest whose salt rotates
 * daily, the referrer is reduced to its host, and Global Privacy Control
 * is honored server-side. The digest exists to count unique visitors
 * within one day; by the next day it cannot be recomputed.
 */
final class AudiencePingController
{
    public function __invoke(Request $request, AudienceSink $sink): Response|JsonResponse
    {
        if (! config('audience.enabled') || $request->headers->get('Sec-GPC') === '1') {
            return response()->noContent();
        }

        // Manual validation: this route has no session, so the exception
        // handler's redirect-with-errors path must never be reachable.
        $validator = Validator::make($request->all(), [
            'path' => ['required', 'string', 'starts_with:/', 'max:200'],
            'locale' => ['required', 'in:en,fr'],
            'referrer' => ['nullable', 'url:http,https', 'max:300'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid ping.'], 422);
        }

        $sink->record($this->event($request, $validator->validated()));

        return response()->noContent();
    }

    /**
     * @param  array{path: string, locale: string, referrer?: string|null}  $input
     * @return array<string, string|null>
     */
    private function event(Request $request, array $input): array
    {
        $date = now()->toDateString();
        $userAgent = (string) $request->userAgent();

        return [
            'date' => $date,
            'path' => $this->normalizePath($input['path']),
            'locale' => $input['locale'],
            'referrer_host' => $this->referrerHost($input['referrer'] ?? null),
            'device' => str_contains($userAgent, 'Mobi') ? 'mobile' : 'desktop',
            'visitor' => $this->visitorDigest($request->ip(), $userAgent, $date),
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    private function normalizePath(string $path): string
    {
        $stripped = (string) preg_replace('#^/(en|fr)(?=/|$)#', '', $path);

        return $stripped === '' ? '/' : $stripped;
    }

    private function referrerHost(?string $referrer): ?string
    {
        if ($referrer === null) {
            return null;
        }

        $host = parse_url($referrer, PHP_URL_HOST);
        $ownHost = parse_url((string) config('site.url'), PHP_URL_HOST);

        return is_string($host) && $host !== $ownHost ? $host : null;
    }

    /**
     * Sixteen hex characters that mean "probably the same visitor, today".
     *
     * The IP is truncated first (IPv4 to /24, IPv6 to /48) so the full
     * address never enters the computation, and the salt includes the date
     * so yesterday's digests cannot be joined with today's.
     */
    private function visitorDigest(?string $ip, string $userAgent, string $date): string
    {
        return substr(hash_hmac(
            'sha256',
            $this->truncateIp($ip).'|'.$userAgent,
            config('app.key').'|'.$date,
        ), 0, 16);
    }

    private function truncateIp(?string $ip): string
    {
        if ($ip === null) {
            return 'unknown';
        }

        if (str_contains($ip, ':')) {
            return implode(':', array_slice(explode(':', $ip), 0, 3)).'::';
        }

        $octets = explode('.', $ip);
        $octets[count($octets) - 1] = '0';

        return implode('.', $octets);
    }
}
```

- [ ] **Step 6: Route.** In `routes/web.php`, above the locale group (add the `use` imports at the top of the file):

```php
use App\Http\Controllers\AudiencePingController;
use App\Http\Middleware\CachePublicResponse;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\ResolvePublicLocale;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/*
 * The T1 audience ping is deliberately stateless: no session, no CSRF
 * (there is no session to protect), no cookie in the response — the
 * absence of state is what makes the endpoint exemptable.
 */
Route::post('/audience', AudiencePingController::class)
    ->name('audience.ping')
    ->withoutMiddleware([
        StartSession::class,
        ShareErrorsFromSession::class,
        ValidateCsrfToken::class,
        ResolvePublicLocale::class,
        HandleInertiaRequests::class,
        AddLinkHeadersForPreloadedAssets::class,
        CachePublicResponse::class,
    ]);
```

- [ ] **Step 7: Run** — `php artisan test --filter=AudiencePingTest` → PASS. Also `php artisan test --filter=SeoAndConsentTest` (the consent share changed shape); if any test pins the old analytics description, repoint it.
- [ ] **Step 8: Pint** — `composer run lint` (PHP path setup per CLAUDE.md), then `composer run lint:check`.
- [ ] **Step 9: Commit** — `git add -A && git commit -m "Count visits without remembering visitors"`.

### Task 2: T1 frontend — the ping sender, opt-out storage, consent share

**Files:**

- Create: `resources/js/lib/audience.ts`
- Modify: `resources/js/app.ts` (idle-scheduled init, after the staticPreview early return)
- Modify: `app/Http/Middleware/HandleInertiaRequests.php` (`consent.audience` block)
- Modify: `resources/js/types/site.ts` (`ConsentConfig` gains `audience`, typed `services.analytics`)
- Modify: `resources/js/types/global.d.ts` (`Navigator.globalPrivacyControl`)
- Test: extend `tests/Feature/AudiencePingTest.php`

**Interfaces:**

- Consumes: `POST /audience` and `config('audience.*')` from Task 1.
- Produces: shared Inertia prop `consent.audience = { enabled: boolean, endpoint: string }`.
- Produces: `resources/js/lib/audience.ts` exporting `initializeAudience(options: { enabled: boolean; endpoint: string }): void`, `isAudienceOptedOut(): boolean`, `setAudienceOptOut(optedOut: boolean): void` — Task 4's controls consume the last two. Opt-out storage key: `sidewalk:audience-opt-out` (localStorage, value `'1'`).

- [ ] **Step 1: Failing test** — add to `AudiencePingTest`:

```php
    public function test_the_consent_share_tells_the_client_where_to_ping(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('"audience":{"enabled":true,"endpoint":"\/audience"}', false);
    }
```

- [ ] **Step 2: Run it** — `php artisan test --filter=test_the_consent_share_tells_the_client_where_to_ping` → FAIL.

- [ ] **Step 3: Share.** In `app/Http/Middleware/HandleInertiaRequests.php`, inside the `'consent' => [...]` block after `'services'`:

```php
                'audience' => [
                    'enabled' => (bool) config('audience.enabled'),
                    'endpoint' => (string) config('audience.endpoint'),
                ],
```

- [ ] **Step 4: Types.** In `resources/js/types/site.ts`, replace the `ConsentConfig` type:

```ts
export type ConsentConfig = {
    mode: string;
    driver: 'none' | 'matomo' | 'posthog';
    cookieName: string;
    categories: ConsentCategory[];
    services: {
        analytics: {
            driver: string;
            posthog: { key: string | null; host: string };
        };
        media: Record<string, unknown>;
    };
    audience: { enabled: boolean; endpoint: string };
};
```

In `resources/js/types/global.d.ts`, at the top level of the `declare global` block (sibling of `interface Window`):

```ts
interface Navigator {
    globalPrivacyControl?: boolean;
}
```

- [ ] **Step 5: The sender.** Create `resources/js/lib/audience.ts`:

```ts
import { router } from '@inertiajs/vue3';
import { readStorage, removeStorage, writeStorage } from '@/lib/safeStorage';

/**
 * Consent tier T1: a cookieless, first-party page-count ping.
 *
 * It stores nothing about the visitor. The only browser state involved is
 * the opt-out itself, and Global Privacy Control is honored both here and
 * on the server, so a ping from a GPC browser is discarded twice.
 */

const OPT_OUT_KEY = 'sidewalk:audience-opt-out';

let initialized = false;
let endpoint = '/audience';
let lastPath: string | null = null;

export function isAudienceOptedOut(): boolean {
    return readStorage('local', OPT_OUT_KEY) === '1';
}

export function setAudienceOptOut(optedOut: boolean): void {
    if (optedOut) {
        writeStorage('local', OPT_OUT_KEY, '1');
    } else {
        removeStorage('local', OPT_OUT_KEY);
    }
}

function ping(path: string): void {
    if (
        isAudienceOptedOut() ||
        navigator.globalPrivacyControl === true ||
        path === lastPath
    ) {
        return;
    }

    lastPath = path;

    const body = new Blob(
        [
            JSON.stringify({
                path,
                locale: document.documentElement.lang === 'fr' ? 'fr' : 'en',
                referrer: document.referrer || null,
            }),
        ],
        { type: 'application/json' },
    );

    if (!navigator.sendBeacon(endpoint, body)) {
        void fetch(endpoint, { method: 'POST', body, keepalive: true }).catch(
            () => {
                /* a lost ping is a rounding error */
            },
        );
    }
}

export function initializeAudience(options: {
    enabled: boolean;
    endpoint: string;
}): void {
    if (initialized || !options.enabled || typeof window === 'undefined') {
        return;
    }

    initialized = true;
    endpoint = options.endpoint;

    ping(window.location.pathname);

    // `lastPath` dedupes the initial navigate event this also fires for,
    // and the second visit cycle a resting pointer's prefetch can cause.
    router.on('navigate', (event) => {
        ping(new URL(event.detail.page.url, window.location.origin).pathname);
    });
}
```

- [ ] **Step 6: Boot it.** In `resources/js/app.ts`, after the `staticPreview` early-return block (the ping must not run in static previews) and beside the other `scheduleIdleTask` calls:

```ts
const consent = props.initialPage.props.consent as ConsentConfig;

scheduleIdleTask(() => {
    void import('@/lib/audience')
        .then(({ initializeAudience }) => {
            initializeAudience(consent.audience);
        })
        .catch(() => {
            /* the ping is expendable */
        });
});
```

Then reuse that `consent` const in the existing `initializeConsent(...)` call (it currently re-casts `props.initialPage.props.consent` inline).

- [ ] **Step 7: Run** — `php artisan test --filter=AudiencePingTest` → PASS; `npm run types:check` → clean; `npx prettier --write` on the shell-edited JS/TS files.
- [ ] **Step 8: Commit** — `git add -A && git commit -m "Send the hello from the browser side"`.

### Task 3: T2 — PostHog EU behind the analytics category

**Files:**

- Run: `npm install posthog-js`
- Create: `resources/js/lib/analytics.ts`
- Modify: `resources/js/lib/consent.ts` (direct dynamic import replaces the CustomEvent placeholder; refreshed analytics copy; `SidewalkConsent.acceptedCategory`)
- Modify: `resources/js/types/global.d.ts` (`SidewalkConsent` signature)
- Test: `npm run types:check`, `npm run build`, bundle spot-check

**Interfaces:**

- Consumes: `ConsentConfig` (Task 2 shape) with `services.analytics.posthog`.
- Produces: `resources/js/lib/analytics.ts` exporting:
    - `enableAnalytics(config: ConsentConfig): void` — dynamic-imports `posthog-js`, inits against the EU host, captures the current `$pageview`, hooks Inertia navigations, then applies the stored replay preference.
    - `disableAnalytics(): void` — stops recording, opts out, resets the device id.
    - `capture(event: string, properties?: Record<string, string>): void` — safe no-op before init and after opt-out; Task 5 consumes it.
    - `setSessionReplay(enabled: boolean): void` and `REPLAY_STORAGE_KEY = 'sidewalk:replay-opt-in'` — Task 4 consumes them.
- Produces: `window.SidewalkConsent` gains `acceptedCategory(category: string): boolean` — Task 4 consumes it.

- [ ] **Step 1: Install** — `npm install posthog-js` (regular dependency; it only ever loads via dynamic import behind consent).

- [ ] **Step 2: The facade.** Create `resources/js/lib/analytics.ts`:

```ts
import { router } from '@inertiajs/vue3';
import { readStorage } from '@/lib/safeStorage';
import type { ConsentConfig } from '@/types';

/**
 * Consent tier T2 (product analytics) and the client half of T3 (replay).
 *
 * posthog-js enters the browser only through the dynamic import below,
 * which only runs once the `analytics` category has been accepted — the
 * library is absent from every bundle a non-consenting visitor loads.
 * Replay (T3) additionally requires its own stored opt-in and is never
 * started by "Accept all" alone.
 */

type PostHog = typeof import('posthog-js').default;

export const REPLAY_STORAGE_KEY = 'sidewalk:replay-opt-in';

let client: PostHog | null = null;
let loading: Promise<PostHog> | null = null;
let navigationHooked = false;

export function enableAnalytics(config: ConsentConfig): void {
    const posthogConfig = config.services.analytics.posthog;

    if (config.driver !== 'posthog' || !posthogConfig.key) {
        return;
    }

    const key = posthogConfig.key;

    loading ??= import('posthog-js').then(({ default: posthog }) => {
        posthog.init(key, {
            api_host: posthogConfig.host,
            autocapture: false,
            capture_pageview: false,
            disable_session_recording: true,
            person_profiles: 'identified_only',
            persistence: 'localStorage',
        });

        return posthog;
    });

    void loading.then((posthog) => {
        client = posthog;

        if (posthog.has_opted_out_capturing()) {
            posthog.opt_in_capturing();
        }

        posthog.capture('$pageview');
        hookNavigation();
        setSessionReplay(readStorage('local', REPLAY_STORAGE_KEY) === '1');
    });
}

export function disableAnalytics(): void {
    if (!client) {
        return;
    }

    client.stopSessionRecording();
    client.opt_out_capturing();
    client.reset();
}

export function capture(
    event: string,
    properties: Record<string, string> = {},
): void {
    client?.capture(event, properties);
}

export function setSessionReplay(enabled: boolean): void {
    if (!client || client.has_opted_out_capturing()) {
        return;
    }

    if (enabled) {
        client.startSessionRecording();
    } else {
        client.stopSessionRecording();
    }
}

function hookNavigation(): void {
    if (navigationHooked) {
        return;
    }

    navigationHooked = true;

    router.on('navigate', () => {
        client?.capture('$pageview');
    });
}
```

- [ ] **Step 3: Rewire consent.** In `resources/js/lib/consent.ts`:

Change `registerDefaults(driver: ConsentConfig['driver'])` to `registerDefaults(config: ConsentConfig)` (update the call in `initializeConsent` to `registerDefaults(config)`), and replace the whole `registerScript({ key: 'analytics-driver', ... })` block — the CustomEvent dispatches were placeholders nothing listens to — with:

```ts
registerScript({
    key: 'analytics-driver',
    category: 'analytics',
    load: async () => {
        if (config.driver === 'none') {
            return;
        }

        const { enableAnalytics } = await import('@/lib/analytics');
        enableAnalytics(config);
    },
    unload: () => {
        void import('@/lib/analytics').then(({ disableAnalytics }) => {
            disableAnalytics();
        });
    },
});
```

Extend the `window.SidewalkConsent` assignment:

```ts
window.SidewalkConsent = {
    showPreferences: () => CookieConsent.showPreferences(),
    acceptedCategory: (category: string) =>
        CookieConsent.acceptedCategory(category),
};
```

and in `resources/js/types/global.d.ts`:

```ts
        SidewalkConsent?: {
            showPreferences: () => void;
            acceptedCategory: (category: string) => boolean;
        };
```

Refresh the analytics copy in both translation objects (the "no-op placeholder" line is no longer true). EN preferences `Analytics` section description becomes:

```
Product analytics through PostHog, hosted in the EU. Used to understand which pages and offers get read. Session replay and heatmaps are a separate, explicit switch on the data-processing page — never part of "Accept all".
```

FR:

```
Mesure d'audience produit via PostHog, hébergé dans l'Union européenne. Sert à comprendre quelles pages et offres sont lues. La relecture de session et les cartes de chaleur ont leur propre interrupteur explicite sur la page traitement des données — jamais inclus dans « Tout accepter ».
```

- [ ] **Step 4: Verify** — `npm run types:check` → clean; `npm run build` → succeeds; then confirm `posthog` appears in no entry chunk: `grep -ril posthog public/build/assets | head` should list only a dedicated lazy chunk, and the app entry file named in `public/build/manifest.json` must not contain `posthog`.
- [ ] **Step 5: Prettier** — `npx prettier --write resources/js/lib/analytics.ts resources/js/lib/consent.ts resources/js/types/global.d.ts`.
- [ ] **Step 6: Commit** — `git add -A && git commit -m "Give the analytics category something real to switch on"`.

### Task 4: T3 replay switch and the measurement controls on /data-processing

**Files:**

- Modify: `app/Content/Schema/PageSchemas.php` (`dataProcessing()` gains a `measurement` group)
- Modify: `resources/content/pages/en/data-processing.md`, `resources/content/pages/fr/data-processing.md`
- Modify: `app/Http/Controllers/SiteController.php` (`dataProcessing()` passes `measurement`)
- Create: `resources/js/copy/en/pages/dataProcessing.ts`, `resources/js/copy/fr/pages/dataProcessing.ts`
- Modify: `resources/js/copy/en/pages/index.ts`, `resources/js/copy/fr/pages/index.ts`
- Create: `resources/js/components/MeasurementControls.vue`
- Modify: `resources/js/pages/DataProcessing.vue`
- Test: `DeclaredPageContentTest` validates the content change; `AudiencePingTest` stays green

**Interfaces:**

- Consumes: `isAudienceOptedOut` / `setAudienceOptOut` (Task 2), `setSessionReplay` / `REPLAY_STORAGE_KEY` (Task 3), `window.SidewalkConsent.acceptedCategory` (Task 3), `cc:onConsent` / `cc:onChange` window events (dispatched by vanilla-cookieconsent), `readStorage` / `writeStorage` / `removeStorage` from `@/lib/safeStorage`.
- Produces: a `measurement` section on `/data-processing` with two native switch rows: audience-ping opt-out (T1) and session-replay opt-in (T3, disabled until `analytics` is accepted).

- [ ] **Step 1: Failing shape check.** In `app/Content/Schema/PageSchemas.php::dataProcessing()`, after the `consent` group add:

```php
            Field::group('measurement', [
                Field::line('eyebrow', 'Eyebrow'),
                Field::line('title', 'Title'),
                Field::text('points', 'Points')->repeating(),
            ], 'Measurement'),
```

Run `php artisan test --filter=DeclaredPageContentTest` → FAIL (both locale files lack `measurement`).

- [ ] **Step 2: Content.** In `resources/content/pages/en/data-processing.md`, after the `consent:` block:

```yaml
measurement:
    eyebrow: Measurement
    title: Three tiers, each with its own switch
    points:
        - 'Audience: a first-party, cookieless ping counts page views with a truncated IP folded into an identifier that changes every day. It stores nothing in your browser, honors Global Privacy Control, and you can switch it off below.'
        - 'Analytics: PostHog, hosted in the EU, loads only after the analytics category is accepted in the consent preferences — never before.'
        - 'Replay and heatmaps: the most invasive tier has its own switch below. Accepting analytics, or pressing "Accept all", never turns it on.'
```

FR twin (same keys, same three-point array):

```yaml
measurement:
    eyebrow: Mesure
    title: 'Trois niveaux, chacun avec son interrupteur'
    points:
        - 'Audience : un ping first-party sans cookie compte les pages vues avec une IP tronquée, fondue dans un identifiant qui change chaque jour. Il ne stocke rien dans votre navigateur, respecte Global Privacy Control, et se désactive ci-dessous.'
        - "Analytics : PostHog, hébergé dans l'Union européenne, ne se charge qu'après acceptation de la catégorie analytics dans les préférences de consentement — jamais avant."
        - "Relecture de session et cartes de chaleur : le niveau le plus intrusif a son propre interrupteur ci-dessous. Accepter les analytics, ou « Tout accepter », ne l'active jamais."
```

- [ ] **Step 3: Controller.** In `SiteController::dataProcessing()`, add `'measurement' => $page['measurement'],` after `'consent'`.

- [ ] **Step 4: Run** — `php artisan test --filter="DeclaredPageContentTest|PublicPagesTest"` → PASS.

- [ ] **Step 5: UI copy.** `resources/js/copy/en/pages/dataProcessing.ts` (keys alphabetical — `sort-keys`):

```ts
export default {
    audienceOptOutHint:
        'Stored in this browser only. Global Privacy Control is honored automatically.',
    audienceOptOutLabel: 'Opt out of the anonymous audience ping',
    openPreferences: 'Open consent preferences',
    replayHint:
        'Off by default and never part of "Accept all". Applies to this browser only.',
    replayHintConsentNeeded:
        'Accept the analytics category first — replay rides on top of it.',
    replayLabel: 'Allow session replay and heatmaps',
} as const;
```

`resources/js/copy/fr/pages/dataProcessing.ts`:

```ts
export default {
    audienceOptOutHint:
        'Mémorisé dans ce navigateur uniquement. Global Privacy Control est respecté automatiquement.',
    audienceOptOutLabel: "Me retirer du ping d'audience anonyme",
    openPreferences: 'Ouvrir les préférences de consentement',
    replayHint:
        'Désactivé par défaut, jamais inclus dans « Tout accepter ». Vaut pour ce navigateur uniquement.',
    replayHintConsentNeeded:
        "Acceptez d'abord la catégorie analytics — la relecture s'appuie dessus.",
    replayLabel: 'Autoriser la relecture de session et les cartes de chaleur',
} satisfies typeof import('../../en/pages/dataProcessing').default;
```

(Match the exact `satisfies` idiom used by the sibling FR modules — read one first.) Register `export { default as dataProcessing } from './dataProcessing';` in **both** `resources/js/copy/{en,fr}/pages/index.ts` (alphabetical position: after `contact`).

- [ ] **Step 6: The controls.** Create `resources/js/components/MeasurementControls.vue`:

```vue
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { copy as copyTree } from '@/copy';
import { isAudienceOptedOut, setAudienceOptOut } from '@/lib/audience';
import { REPLAY_STORAGE_KEY, setSessionReplay } from '@/lib/analytics';
import { readStorage, removeStorage, writeStorage } from '@/lib/safeStorage';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();

const copy = computed(
    () => copyTree[page.props.site.locale].pages.dataProcessing,
);

const audienceOptedOut = ref(false);
const replayEnabled = ref(false);
const analyticsAccepted = ref(false);

function refreshConsent(): void {
    analyticsAccepted.value =
        window.SidewalkConsent?.acceptedCategory('analytics') ?? false;
}

onMounted(() => {
    audienceOptedOut.value = isAudienceOptedOut();
    replayEnabled.value = readStorage('local', REPLAY_STORAGE_KEY) === '1';
    refreshConsent();
    window.addEventListener('cc:onConsent', refreshConsent);
    window.addEventListener('cc:onChange', refreshConsent);
});

onBeforeUnmount(() => {
    window.removeEventListener('cc:onConsent', refreshConsent);
    window.removeEventListener('cc:onChange', refreshConsent);
});

function toggleAudience(event: Event): void {
    const optedOut = !(event.target as HTMLInputElement).checked;
    audienceOptedOut.value = optedOut;
    setAudienceOptOut(optedOut);
}

function toggleReplay(event: Event): void {
    const enabled = (event.target as HTMLInputElement).checked;
    replayEnabled.value = enabled;

    if (enabled) {
        writeStorage('local', REPLAY_STORAGE_KEY, '1');
    } else {
        removeStorage('local', REPLAY_STORAGE_KEY);
    }

    setSessionReplay(enabled);
}

function openPreferences(): void {
    window.SidewalkConsent?.showPreferences();
}
</script>

<template>
    <div class="measurement-controls">
        <label class="measurement-controls__row">
            <input
                type="checkbox"
                class="measurement-controls__switch"
                :checked="!audienceOptedOut"
                @change="toggleAudience"
            />
            <span class="measurement-controls__text">
                <span class="measurement-controls__label">{{
                    copy.audienceOptOutLabel
                }}</span>
                <span class="measurement-controls__hint type-body-sm">{{
                    copy.audienceOptOutHint
                }}</span>
            </span>
        </label>

        <label class="measurement-controls__row">
            <input
                type="checkbox"
                class="measurement-controls__switch"
                :checked="replayEnabled"
                :disabled="!analyticsAccepted"
                @change="toggleReplay"
            />
            <span class="measurement-controls__text">
                <span class="measurement-controls__label">{{
                    copy.replayLabel
                }}</span>
                <span class="measurement-controls__hint type-body-sm">
                    {{
                        analyticsAccepted
                            ? copy.replayHint
                            : copy.replayHintConsentNeeded
                    }}
                    <button
                        v-if="!analyticsAccepted"
                        type="button"
                        class="measurement-controls__preferences"
                        @click="openPreferences"
                    >
                        {{ copy.openPreferences }}
                    </button>
                </span>
            </span>
        </label>
    </div>
</template>
```

Style it in the component's scoped block with existing `--sw-*` tokens only (spacing, line/border and form/accent tokens the Contact form's controls already use — read `Contact.vue`'s selects and `docs/style/components.md` first; `accent-color` on the checkbox must come from a token). The semantic native checkbox is the platform primitive; no custom switch component.

- [ ] **Step 7: Mount it.** In `resources/js/pages/DataProcessing.vue`: accept the new prop (`measurement: { eyebrow: string; title: string; points: string[] }`), render a section matching the existing `storage`/`consent` sections' markup (same heading/points treatment — read the file and mirror it), with `<MeasurementControls />` after the points list.

- [ ] **Step 8: Visual pass** — `npm run dev`, check `/en/data-processing` and `/fr/data-processing` in both `morning` and `sunset` themes: switch rows legible, disabled state visibly muted, no green/amber drift on sunset accents.

- [ ] **Step 9: Reviews** — dispatch `design-conformance-reviewer` on `MeasurementControls.vue` + `DataProcessing.vue`, and `i18n-parity-reviewer` on the two `data-processing.md` files; fix findings.

- [ ] **Step 10: Run** — `php artisan test --filter="DeclaredPageContentTest|PublicPagesTest|LanguageFileParityTest"` → PASS; `npm run types:check` → clean; `npx prettier --write` on every JS/Vue file shell-edited in this task.

- [ ] **Step 11: Commit** — `git add -A && git commit -m "Put the invasive tier behind its own switch"`.

### Task 5: Funnel-stage events V1–V3

**Files:**

- Create: `resources/js/composables/useReaderEngagement.ts`
- Modify: `resources/js/pages/Writing/Show.vue`, `resources/js/pages/CaseStudies/Show.vue` (V1 sentinel)
- Modify: `resources/js/pages/Services.vue` (V2 on mount)
- Modify: `resources/js/pages/Contact.vue` (V3 on both handoff channels)
- Test: `npm run types:check`; existing feature tests stay green

**Interfaces:**

- Consumes: `capture(event, properties)` from Task 3 (safe no-op without consent — call sites never check).
- Produces: the funnel event vocabulary (documented in Task 6):
    - V0 — `$pageview` (T2) and `audience_ping` (T1), automatic.
    - V1 — `reader_engaged` `{ funnel_stage: 'V1', section: 'journal' | 'case-studies' }`, fired once when the end of an article enters the viewport.
    - V2 — `services_viewed` `{ funnel_stage: 'V2' }`, fired when `/services` mounts.
    - V3 — `lead_intent` `{ funnel_stage: 'V3', channel: 'email' | 'whatsapp' }`, fired when the contact form composes its mailto or the WhatsApp handoff is clicked.
    - V4 — a signed engagement is an offline fact; it is recorded manually in PostHog, never by the site.

- [ ] **Step 1: The composable.** Create `resources/js/composables/useReaderEngagement.ts`:

```ts
import { onBeforeUnmount, onMounted } from 'vue';
import type { Ref } from 'vue';
import { capture } from '@/lib/analytics';

/**
 * Funnel stage V1: someone actually read the piece.
 *
 * "Read" means the end of the article entered the viewport — an
 * IntersectionObserver on a sentinel, not a timer. Fires at most once per
 * page view, and only ever reaches PostHog when the analytics category
 * has been accepted (capture() is a no-op otherwise).
 */
export function useReaderEngagement(
    sentinel: Ref<HTMLElement | null>,
    section: 'journal' | 'case-studies',
): void {
    let observer: IntersectionObserver | null = null;

    onMounted(() => {
        if (!sentinel.value || typeof IntersectionObserver === 'undefined') {
            return;
        }

        observer = new IntersectionObserver((entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                capture('reader_engaged', {
                    funnel_stage: 'V1',
                    section,
                });
                observer?.disconnect();
                observer = null;
            }
        });

        observer.observe(sentinel.value);
    });

    onBeforeUnmount(() => {
        observer?.disconnect();
    });
}
```

- [ ] **Step 2: V1 sentinels.** In `resources/js/pages/Writing/Show.vue`, script setup gains:

```ts
import { ref } from 'vue'; // merge into the existing vue import
import { useReaderEngagement } from '@/composables/useReaderEngagement';

const engagementSentinel = ref<HTMLElement | null>(null);
useReaderEngagement(engagementSentinel, 'journal');
```

and the `#article` template slot gains, after `<RichText :html="props.item.body_html" />`:

```html
<div ref="engagementSentinel" aria-hidden="true"></div>
```

Same change in `resources/js/pages/CaseStudies/Show.vue` with `'case-studies'` (its `#article` slot is at ~line 76; place the sentinel after the last article child).

- [ ] **Step 3: V2.** In `resources/js/pages/Services.vue` script setup:

```ts
import { onMounted } from 'vue'; // merge into the existing vue import
import { capture } from '@/lib/analytics';

onMounted(() => {
    capture('services_viewed', { funnel_stage: 'V2' });
});
```

- [ ] **Step 4: V3.** In `resources/js/pages/Contact.vue`: import `capture` from `@/lib/analytics`; in `submitInquiry()` add `capture('lead_intent', { funnel_stage: 'V3', channel: 'email' });` before the `window.location.href` assignment; on the WhatsApp anchor (the `contact-page__whatsapp-button` element, ~line 165) add a click handler calling `capture('lead_intent', { funnel_stage: 'V3', channel: 'whatsapp' })` — expose a small named handler in the script rather than an inline expression if lint objects.

- [ ] **Step 5: Run** — `npm run types:check` → clean; `php artisan test --filter="PublicPagesTest|ContactQualificationTest"` → PASS (the pages still render); `npx prettier --write` on the touched files.
- [ ] **Step 6: Review** — dispatch `design-conformance-reviewer` on the four modified Vue files (template changes are tiny but the sentinel must not affect layout: zero-height, no margin).
- [ ] **Step 7: Commit** — `git add -A && git commit -m "Name the steps a stranger takes toward a first call"`.

### Task 6: Documentation, baseline, push

**Files:**

- Create: `docs/architecture/measurement.md`
- Modify: `README.md` (one line in the "what it demonstrates" list if measurement is absent)
- Modify: `docs/superpowers/plans/2026-09-01-s2-measurement.md` (mark executed)

- [ ] **Step 1: The measurement doc.** Create `docs/architecture/measurement.md` covering, in this order: the T0–T4 tier table from the spec (T4 marked "S4, not yet built"); the V0–V4 funnel-stage table with the exact event names and properties from Task 5; the T1 endpoint contract and its CNIL-exemption design choices (stateless route, truncated IP, daily-rotating HMAC, GPC honored server-side, client opt-out, referrer reduced to host); the sink model and why the default is the logger (no database in prod); and the activation runbook — create a PostHog **EU Cloud** project, then set `ANALYTICS_DRIVER=posthog`, `POSTHOG_KEY=phc_…`, and optionally `AUDIENCE_SINK=posthog` in Vercel env; no deploy needed beyond the env change. Close with the boundary: raw IPs and full UAs never leave the request scope; `resources/content/**` privacy copy is the user-facing twin of this doc.
- [ ] **Step 2: README.** If the feature list does not yet mention measurement, add one line alongside the consent-first bullet: the three-tier consent design with a first-party CNIL-exemptable audience ping, PostHog EU behind opt-in, replay behind its own switch.
- [ ] **Step 3: Full baseline** — in order: `npm run check` → clean; `composer run lint:check` → clean; `php artisan test` → all green; `npm run build` → succeeds.
- [ ] **Step 4: Invoke `superpowers:verification-before-completion`** — evidence before claims; fix anything red and re-run.
- [ ] **Step 5: Mark this plan executed** (status line at the top, like the S1 plan), commit — `git add -A && git commit -m "Write down how the counting works"` — then `git push` (deploys to Vercel).
- [ ] **Step 6: Post-deploy smoke** — the Vercel CLI is not logged in on this machine; if the prod URL is known from the repo (check `config('site.url')`), `curl -s -o /dev/null -w '%{http_code}' -X POST https://<prod>/audience -H 'Content-Type: application/json' -d '{"path":"/en","locale":"en"}'` expecting 204, and load `/en/data-processing` expecting 200. If the URL is not reachable from here, note it for Isma instead of guessing.

## Self-review notes

- Spec coverage: §6 T1 (Tasks 1–2), T2 (Task 3), T3 (Task 4), §3 funnel stages as shared vocabulary (Task 5, doc in Task 6). The `marketing` consent category and everything T4 is S4 scope by §11 and stays out. "Open metrics" (§6) is a public dashboard, S5 "Show" material — deliberately out. The T1 journal post is §9 content-wave scope — out.
- Type consistency: the event array keys produced by `AudiencePingController::event()` match what both sinks and every Task 1 test consume (`date/path/locale/referrer_host/device/visitor/occurred_at`); `ConsentConfig.audience` produced in Task 2 matches `initializeAudience`'s options and `HandleInertiaRequests`' share; `REPLAY_STORAGE_KEY`/`setSessionReplay` names match between Tasks 3 and 4; `capture`'s signature matches between Tasks 3 and 5.
- The stateless route excludes exactly the middleware that would create state (session, CSRF-over-session, Inertia share, response cache, locale resolution); cookie-encryption middleware stays and is inert with no cookies queued.
- Zero-env degradation: driver `none` keeps Task 3 dormant (the registry's `load` returns early), missing `POSTHOG_KEY` keeps both the client init and the server sink quiet, `AUDIENCE_SINK=log` needs nothing external.
