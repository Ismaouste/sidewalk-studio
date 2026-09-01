<?php

namespace Tests\Feature;

use App\Services\PageContentRepository;
use Tests\TestCase;

class ContactBookingTest extends TestCase
{
    public function test_the_contact_payload_carries_a_booking_group_with_an_empty_url_by_default(): void
    {
        $page = app(PageContentRepository::class)->get('contact', 'en');

        $this->assertArrayHasKey('booking', $page);
        $this->assertSame('', $page['booking']['url']);
        $this->assertNotSame('', $page['booking']['title']);
    }

    public function test_both_locales_declare_the_same_booking_shape(): void
    {
        $repository = app(PageContentRepository::class);

        $this->assertSame(
            array_keys($repository->get('contact', 'en')['booking']),
            array_keys($repository->get('contact', 'fr')['booking']),
        );
    }

    public function test_the_contact_page_still_renders_with_the_new_group(): void
    {
        $this->get('/en/contact')->assertOk();
        $this->get('/fr/contact')->assertOk();
    }
}
