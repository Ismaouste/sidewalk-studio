<?php

namespace App\Newsletter;

interface NewsletterDriver
{
    /**
     * Segment is one of 'engineering' | 'local-business'; locale 'en' | 'fr'.
     *
     * @throws NewsletterDeliveryException when the provider refused the address
     */
    public function subscribe(string $email, string $segment, string $locale): void;
}
