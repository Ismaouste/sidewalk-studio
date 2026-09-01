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
