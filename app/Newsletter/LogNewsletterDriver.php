<?php

namespace App\Newsletter;

use Illuminate\Support\Facades\Log;

final class LogNewsletterDriver implements NewsletterDriver
{
    public function subscribe(string $email, string $segment, string $locale): void
    {
        [$local, $domain] = explode('@', $email, 2);

        Log::info('newsletter.subscribed', [
            // Masked on purpose: function logs are drained off-platform and
            // a newsletter address is PII the log sink has no business holding.
            'email' => mb_substr($local, 0, 1).'***@'.$domain,
            'segment' => $segment,
            'locale' => $locale,
        ]);
    }
}
