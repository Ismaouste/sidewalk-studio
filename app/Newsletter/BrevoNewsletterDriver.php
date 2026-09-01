<?php

namespace App\Newsletter;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class BrevoNewsletterDriver implements NewsletterDriver
{
    public function subscribe(string $email, string $segment, string $locale): void
    {
        $key = config('newsletter.brevo.key');

        if (! is_string($key) || $key === '') {
            // Same posture as the PostHog sink without a key: the driver is
            // selected but unconfigured, so the signup degrades to a note.
            Log::info('newsletter.driver_unconfigured', ['segment' => $segment]);

            return;
        }

        $redirect = rtrim((string) config('site.url'), '/')."/{$locale}/newsletter/confirmed";

        try {
            $response = Http::timeout(8)
                ->withHeaders(['api-key' => $key])
                ->post('https://api.brevo.com/v3/contacts/doubleOptinConfirmation', [
                    'email' => $email,
                    'includeListIds' => [(int) config("newsletter.brevo.lists.{$segment}")],
                    'templateId' => (int) config('newsletter.brevo.doi_template_id'),
                    'redirectionUrl' => $redirect,
                    'attributes' => ['LOCALE' => $locale, 'SEGMENT' => $segment],
                ]);
        } catch (Throwable $exception) {
            throw new NewsletterDeliveryException('Brevo is unreachable.', 0, $exception);
        }

        if ($response->failed()) {
            throw new NewsletterDeliveryException('Brevo refused the subscription: '.$response->status());
        }
    }
}
