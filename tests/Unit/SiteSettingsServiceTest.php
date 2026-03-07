<?php

namespace Tests\Unit;

use App\Models\SiteSetting;
use App\Services\SiteSettingsService;
use App\SiteSettings\Data\ContactDetailsSettings;
use App\SiteSettings\Data\SeoDefaultsSettings;
use App\SiteSettings\Data\SiteIdentitySettings;
use App\SiteSettings\InvalidSiteSettings;
use App\SiteSettings\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SiteSettingsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_default_settings_without_persisting_a_row(): void
    {
        $settings = app(SiteSettingsService::class)->current();

        $this->assertInstanceOf(SiteSettings::class, $settings);
        $this->assertInstanceOf(SiteIdentitySettings::class, $settings->siteIdentity);
        $this->assertInstanceOf(ContactDetailsSettings::class, $settings->contactDetails);
        $this->assertInstanceOf(SeoDefaultsSettings::class, $settings->seoDefaults);
        $this->assertSame(config('site.name'), $settings->siteIdentity->name);
        $this->assertSame(config('site.contact.email'), $settings->contactDetails->email);
        $this->assertDatabaseCount('site_settings', 0);
    }

    public function test_it_reads_persisted_settings_into_typed_groups(): void
    {
        SiteSetting::query()->create([
            'id' => SiteSetting::SINGLETON_ID,
            ...$this->payload([
                'site_identity' => [
                    'name' => 'Studio Atlas',
                    'tagline' => 'A settings-backed tagline.',
                ],
                'contact_details' => [
                    'email' => 'hello@studio-atlas.test',
                    'location' => 'Remote-first',
                ],
                'social_links' => [
                    'github_url' => 'https://github.com/studio-atlas',
                ],
                'seo_defaults' => [
                    'title_suffix' => 'Studio Atlas',
                    'default_description' => 'Settings-backed metadata defaults.',
                ],
            ]),
        ]);

        $settings = app(SiteSettingsService::class)->refresh();

        $this->assertSame('Studio Atlas', $settings->siteIdentity->name);
        $this->assertSame('hello@studio-atlas.test', $settings->contactDetails->email);
        $this->assertSame('https://github.com/studio-atlas', $settings->socialLinks->githubUrl);
        $this->assertSame('Settings-backed metadata defaults.', $settings->seoDefaults->defaultDescription);
    }

    public function test_it_caches_values_until_the_cache_is_explicitly_forgotten(): void
    {
        $service = app(SiteSettingsService::class);
        $defaultName = $service->current()->siteIdentity->name;

        DB::table('site_settings')->insert($this->rawRecord([
            'site_identity' => [
                'name' => 'Cached Override',
            ],
        ]));

        $this->assertSame($defaultName, $service->current()->siteIdentity->name);
        $this->assertSame('Cached Override', $service->refresh()->siteIdentity->name);
    }

    public function test_model_writes_flush_the_cached_settings(): void
    {
        $service = app(SiteSettingsService::class);
        $service->current();

        SiteSetting::query()->create([
            'id' => SiteSetting::SINGLETON_ID,
            ...$this->payload([
                'site_identity' => [
                    'name' => 'Fresh Settings',
                ],
            ]),
        ]);

        $this->assertSame('Fresh Settings', $service->current()->siteIdentity->name);
    }

    public function test_bootstrap_creates_the_singleton_once_and_does_not_overwrite_existing_values(): void
    {
        $service = app(SiteSettingsService::class);

        $created = $service->bootstrap();
        $this->assertSame(SiteSetting::SINGLETON_ID, $created->id);
        $this->assertDatabaseCount('site_settings', 1);

        $created->update([
            'site_identity' => array_replace($created->site_identity, [
                'name' => 'Operator Override',
            ]),
        ]);

        $bootstrappedAgain = $service->bootstrap();

        $this->assertSame('Operator Override', $bootstrappedAgain->site_identity['name']);
        $this->assertDatabaseCount('site_settings', 1);
    }

    public function test_invalid_persisted_values_are_rejected_when_the_service_hydrates_them(): void
    {
        SiteSetting::query()->create([
            'id' => SiteSetting::SINGLETON_ID,
            ...$this->payload([
                'contact_details' => [
                    'email' => 'not-an-email',
                ],
            ]),
        ]);

        $this->expectException(InvalidSiteSettings::class);

        app(SiteSettingsService::class)->refresh();
    }

    protected function payload(array $overrides = []): array
    {
        $defaults = app(SiteSettingsService::class)->defaults()->toPersistenceArray();

        return array_replace_recursive($defaults, $overrides);
    }

    protected function rawRecord(array $overrides = []): array
    {
        $payload = $this->payload($overrides);

        return [
            'id' => SiteSetting::SINGLETON_ID,
            'site_identity' => json_encode($payload['site_identity']),
            'contact_details' => json_encode($payload['contact_details']),
            'social_links' => json_encode($payload['social_links']),
            'seo_defaults' => json_encode($payload['seo_defaults']),
            'consent_copy' => json_encode($payload['consent_copy']),
            'feature_toggles' => json_encode($payload['feature_toggles']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
