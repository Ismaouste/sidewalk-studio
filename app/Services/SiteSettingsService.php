<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\SiteSettings\SiteSettings;
use App\Support\PublicLocale;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class SiteSettingsService
{
    public const CACHE_KEY = 'site_settings.current';

    public function current(?string $locale = null): SiteSettings
    {
        $locale ??= app()->getLocale();

        if ($this->source() === 'database') {
            return $this->databaseCurrent();
        }

        return Cache::rememberForever(
            $this->publicCacheKey($locale),
            fn (): SiteSettings => SiteSettings::fromPayload($this->defaultPayload($locale)),
        );
    }

    public function defaults(?string $locale = null): SiteSettings
    {
        return SiteSettings::fromPayload($this->defaultPayload($locale ?? PublicLocale::default()));
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
        Cache::forget($this->databaseCacheKey());

        foreach (PublicLocale::supported() as $locale) {
            Cache::forget($this->publicCacheKey($locale));
        }
    }

    public function refresh(): SiteSettings
    {
        $this->forget();

        return $this->databaseCurrent();
    }

    protected function databaseCurrent(): SiteSettings
    {
        return Cache::rememberForever(
            $this->databaseCacheKey(),
            fn (): SiteSettings => SiteSettings::fromPayload($this->resolvedDatabasePayload()),
        );
    }

    protected function resolvedDatabasePayload(): array
    {
        $defaults = $this->defaultPayload(PublicLocale::default());

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
            'theme_settings' => $record->theme_settings ?? [],
        ]);
    }

    protected function defaultPayload(string $locale): array
    {
        $site = config('site');
        $localized = $this->localizedDefaults($locale);
        $contact = $site['contact'] ?? [];
        $profiles = array_values(array_filter($site['author']['same_as'] ?? []));
        $theme = $site['theme'] ?? [];

        return [
            'site_identity' => [
                'name' => (string) ($localized['site_identity']['name'] ?? $site['name'] ?? config('app.name')),
                'tagline' => (string) ($localized['site_identity']['tagline'] ?? $site['tagline'] ?? ''),
                'description' => (string) ($localized['site_identity']['description'] ?? $site['description'] ?? ''),
            ],
            'contact_details' => [
                'email' => (string) ($localized['contact_details']['email'] ?? $contact['email'] ?? ($site['author']['email'] ?? 'hello@sidewalk-studio.test')),
                'location' => (string) ($localized['contact_details']['location'] ?? $contact['location'] ?? ''),
                'availability' => (string) ($localized['contact_details']['availability'] ?? $contact['availability'] ?? ''),
            ],
            'social_links' => [
                'github_url' => $this->firstProfileUrlContaining($profiles, 'github.com'),
                'linkedin_url' => $this->firstProfileUrlContaining($profiles, 'linkedin.com'),
            ],
            'seo_defaults' => [
                'title_suffix' => (string) ($localized['site_identity']['name'] ?? $site['name'] ?? config('app.name')),
                'default_description' => (string) ($localized['site_identity']['description'] ?? $site['description'] ?? ''),
                'default_robots' => 'index,follow',
            ],
            'consent_copy' => [
                'preferences_title' => (string) ($localized['consent_copy']['preferences_title'] ?? 'Consent preferences'),
                'preferences_description' => (string) ($localized['consent_copy']['preferences_description'] ?? 'Choose which optional services may load on this site. Analytics stays disabled unless you opt in.'),
                'media_notice_title' => (string) ($localized['consent_copy']['media_notice_title'] ?? 'External media is blocked by default'),
                'media_notice_description' => (string) ($localized['consent_copy']['media_notice_description'] ?? 'Accept the media category to load third-party embeds such as YouTube.'),
            ],
            'feature_toggles' => [
                'show_labs' => true,
                'show_writing' => true,
                'show_case_studies' => true,
            ],
            'theme_settings' => [
                'morning_accent' => (string) ($theme['morning']['accent'] ?? '#8a7258'),
                'morning_glow' => (string) ($theme['morning']['glow'] ?? '#cf6445'),
                'morning_glow_soft' => (string) ($theme['morning']['glow_soft'] ?? '#f1c58d'),
                'sunset_accent' => (string) ($theme['sunset']['accent'] ?? '#d6d9df'),
                'sunset_glow' => (string) ($theme['sunset']['glow'] ?? '#d38b76'),
                'sunset_glow_soft' => (string) ($theme['sunset']['glow_soft'] ?? '#9a7db1'),
                'header_gradient_angle' => (int) ($theme['header_gradient_angle'] ?? 160),
                'ambient_blur_px' => (int) ($theme['ambient_blur_px'] ?? 136),
                'grid_line_px' => (float) ($theme['grid_line_px'] ?? 1),
            ],
        ];
    }

    protected function source(): string
    {
        $source = strtolower((string) config('site.settings_source', 'files'));

        return in_array($source, ['files', 'database'], true) ? $source : 'files';
    }

    protected function publicCacheKey(string $locale): string
    {
        return sprintf('%s.public.%s', self::CACHE_KEY, $locale);
    }

    protected function databaseCacheKey(): string
    {
        return sprintf('%s.database', self::CACHE_KEY);
    }

    protected function localizedDefaults(string $locale): array
    {
        $fallback = $this->loadLocalizedDefaults(PublicLocale::default());

        if ($locale === PublicLocale::default()) {
            return $fallback;
        }

        return array_replace_recursive($fallback, $this->loadLocalizedDefaults($locale));
    }

    protected function loadLocalizedDefaults(string $locale): array
    {
        $path = lang_path(sprintf('%s/site.php', $locale));

        if (! File::exists($path)) {
            return [];
        }

        $payload = require $path;

        return is_array($payload['settings'] ?? null)
            ? $payload['settings']
            : [];
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
