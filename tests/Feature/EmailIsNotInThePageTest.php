<?php

namespace Tests\Feature;

use App\Services\SiteSettingsService;
use App\Support\ObfuscatedEmail;
use Tests\TestCase;

/**
 * The contact address is not written into any page.
 *
 * Page props end up in the document, so a plain address in them is a plain address in the HTML. It travels encoded and the browser
 * decodes it once the page has loaded, when the mailto link appears. This pins the part the server owns: the HTML of the public pages
 * holds no plain address, in the props, in the structured data or in the markup.
 */
class EmailIsNotInThePageTest extends TestCase
{
    public function test_the_encoding_round_trips_and_is_not_plain(): void
    {
        $encoded = ObfuscatedEmail::encode('someone@example.test');

        $this->assertStringStartsWith('b64:', $encoded);
        $this->assertStringNotContainsString('someone', $encoded);
        $this->assertSame('someone@example.test', ObfuscatedEmail::decode($encoded));
        $this->assertSame('', ObfuscatedEmail::encode(''));
        $this->assertSame('', ObfuscatedEmail::encode(null));
        $this->assertSame('plain@example.test', ObfuscatedEmail::decode('plain@example.test'));
        $this->assertSame('', ObfuscatedEmail::decode('b64:***not base64***'));
    }

    public function test_no_public_page_holds_the_plain_address(): void
    {
        $contact = app(SiteSettingsService::class)->current()->contactDetails->email;
        $author = (string) config('site.author.email');

        foreach (['/en', '/fr', '/en/contact', '/en/experience', '/en/data-processing', '/en/services'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            foreach (array_filter([$contact, $author]) as $address) {
                $this->assertStringNotContainsString($address, $html, "{$path} must not hold {$address}.");
                $this->assertStringNotContainsString(str_replace('@', '\u0040', $address), $html);
            }
        }
    }

    public function test_the_contact_page_hands_the_address_over_encoded(): void
    {
        $html = $this->get('/en/contact')->assertOk()->getContent();
        $contact = app(SiteSettingsService::class)->current()->contactDetails->email;

        $this->assertStringContainsString(base64_encode($contact), $html);
    }
}
