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
