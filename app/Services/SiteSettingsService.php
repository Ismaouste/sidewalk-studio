<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\SiteSettings\SiteSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SiteSettingsService
{
    public const CACHE_KEY = 'site_settings.current';

    public function current(): SiteSettings
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): SiteSettings => $this->resolve());
    }

    public function defaults(): SiteSettings
    {
        return SiteSettings::fromPayload($this->defaultPayload());
    }

    public function bootstrap(): SiteSetting
    {
        $existing = SiteSetting::query()->find(SiteSetting::SINGLETON_ID);

        if ($existing instanceof SiteSetting) {
            return $existing;
        }

        $settings = $this->defaults();

        return SiteSetting::query()->create([
            'id' => SiteSetting::SINGLETON_ID,
            ...$settings->toPersistenceArray(),
        ]);
    }

    public function update(array $payload): SiteSettings
    {
        $settings = $this->hydrate($payload);

        $this->store($settings);

        return $this->refresh();
    }

    public function hydrate(array $payload): SiteSettings
    {
        return SiteSettings::fromPayload($payload);
    }

    public function store(SiteSettings $settings): void
    {
        SiteSetting::query()->updateOrCreate(
            ['id' => SiteSetting::SINGLETON_ID],
            $settings->toPersistenceArray(),
        );
    }

    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function refresh(): SiteSettings
    {
        $this->forget();

        return $this->current();
    }

    protected function resolve(): SiteSettings
    {
        return SiteSettings::fromPayload($this->resolvedPayload());
    }

    protected function resolvedPayload(): array
    {
        $defaults = $this->defaultPayload();

        if (! Schema::hasTable('site_settings')) {
            return $defaults;
        }

        $record = SiteSetting::query()->find(SiteSetting::SINGLETON_ID);

        if (! $record instanceof SiteSetting) {
            return $defaults;
        }

        return array_replace_recursive($defaults, [
            'site_identity' => $record->site_identity ?? [],
            'contact_details' => $record->contact_details ?? [],
            'social_links' => $record->social_links ?? [],
            'seo_defaults' => $record->seo_defaults ?? [],
            'consent_copy' => $record->consent_copy ?? [],
            'feature_toggles' => $record->feature_toggles ?? [],
        ]);
    }

    protected function defaultPayload(): array
    {
        $site = config('site');
        $contact = $site['contact'] ?? [];
        $profiles = array_values(array_filter($site['author']['same_as'] ?? []));

        return [
            'site_identity' => [
                'name' => (string) ($site['name'] ?? config('app.name')),
                'tagline' => (string) ($site['tagline'] ?? ''),
                'description' => (string) ($site['description'] ?? ''),
            ],
            'contact_details' => [
                'email' => (string) ($contact['email'] ?? ($site['author']['email'] ?? 'hello@sidewalk-studio.test')),
                'location' => (string) ($contact['location'] ?? ''),
                'availability' => (string) ($contact['availability'] ?? ''),
            ],
            'social_links' => [
                'github_url' => $this->firstProfileUrlContaining($profiles, 'github.com'),
                'linkedin_url' => $this->firstProfileUrlContaining($profiles, 'linkedin.com'),
            ],
            'seo_defaults' => [
                'title_suffix' => (string) ($site['name'] ?? config('app.name')),
                'default_description' => (string) ($site['description'] ?? ''),
                'default_robots' => 'index,follow',
            ],
            'consent_copy' => [
                'preferences_title' => 'Consent preferences',
                'preferences_description' => 'Choose which optional services may load on this site. Analytics stays disabled unless you opt in.',
                'media_notice_title' => 'External media is blocked by default',
                'media_notice_description' => 'Accept the media category to load third-party embeds such as YouTube.',
            ],
            'feature_toggles' => [
                'show_labs' => true,
                'show_writing' => true,
                'show_case_studies' => true,
            ],
        ];
    }

    protected function firstProfileUrlContaining(array $profiles, string $needle): ?string
    {
        foreach ($profiles as $profile) {
            if (! is_string($profile)) {
                continue;
            }

            if (str_contains(strtolower($profile), strtolower($needle))) {
                return trim($profile);
            }
        }

        return null;
    }
}
