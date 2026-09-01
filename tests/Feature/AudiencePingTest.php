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

        $this->events = new ArrayObject;
        $events = $this->events;

        $this->app->instance(AudienceSink::class, new class($events) implements AudienceSink
        {
            public function __construct(private ArrayObject $events) {}

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

        $this->call('POST', '/audience', [], [], [], ['REMOTE_ADDR' => '203.0.113.5'] + $server, $payload);
        $this->call('POST', '/audience', [], [], [], ['REMOTE_ADDR' => '203.0.113.99'] + $server, $payload);
        $this->call('POST', '/audience', [], [], [], [
            'REMOTE_ADDR' => '203.0.113.5',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_0 like Mac OS X) Mobile/15E148',
        ] + $server, $payload);

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

    public function test_the_consent_share_tells_the_client_where_to_ping(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('"audience":{"enabled":true,"endpoint":"\/audience"}', false);
    }

    public function test_the_log_sink_writes_one_structured_line(): void
    {
        Log::spy();

        (new LogAudienceSink)->record(['path' => '/services', 'visitor' => 'abc']);

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
        (new PostHogAudienceSink)->record($event);
        Http::assertNothingSent();

        config(['consent.services.analytics.posthog.key' => 'phc_test']);
        (new PostHogAudienceSink)->record($event);

        Http::assertSent(function ($request): bool {
            return str_starts_with($request->url(), 'https://eu.i.posthog.com/')
                && $request['api_key'] === 'phc_test'
                && $request['event'] === 'audience_ping'
                && $request['distinct_id'] === 'abc'
                && $request['properties']['$process_person_profile'] === false;
        });
    }
}
