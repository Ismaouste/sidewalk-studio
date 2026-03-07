<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Services\SiteSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SiteSettingsPublicIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_uses_settings_for_shared_site_props_and_metadata(): void
    {
        SiteSetting::query()->create([
            'id' => SiteSetting::SINGLETON_ID,
            ...$this->payload([
                'site_identity' => [
                    'name' => 'Studio Atlas',
                    'tagline' => 'A settings-backed tagline.',
                    'description' => 'A settings-backed long description.',
                ],
                'contact_details' => [
                    'email' => 'hello@studio-atlas.test',
                ],
                'social_links' => [
                    'github_url' => 'https://github.com/studio-atlas',
                    'linkedin_url' => 'https://www.linkedin.com/company/studio-atlas',
                ],
                'seo_defaults' => [
                    'title_suffix' => 'Studio Atlas',
                    'default_description' => 'A tailored metadata default for the public site.',
                ],
            ]),
        ]);

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->where('name', 'Studio Atlas')
                ->where('site.name', 'Studio Atlas')
                ->where('site.tagline', 'A settings-backed tagline.')
                ->where('site.contact.email', 'hello@studio-atlas.test'))
            ->assertSee('A tailored metadata default for the public site.')
            ->assertSee('https://github.com/studio-atlas', false);
    }

    public function test_contact_page_uses_settings_backed_contact_details(): void
    {
        SiteSetting::query()->create([
            'id' => SiteSetting::SINGLETON_ID,
            ...$this->payload([
                'contact_details' => [
                    'email' => 'contact@studio-atlas.test',
                    'location' => 'Remote-first',
                    'availability' => 'Booked for Q2 launches with room for targeted architecture work.',
                ],
            ]),
        ]);

        $this->get('/contact')
            ->assertOk()
            ->assertSee('contact@studio-atlas.test')
            ->assertSee('Remote-first')
            ->assertSee('Booked for Q2 launches with room for targeted architecture work.');
    }

    protected function payload(array $overrides = []): array
    {
        $defaults = app(SiteSettingsService::class)->defaults()->toPersistenceArray();

        return array_replace_recursive($defaults, $overrides);
    }
}
