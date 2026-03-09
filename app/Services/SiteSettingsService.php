<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\SiteSettings\SiteSettings;
use Carbon\CarbonImmutable;
use App\Support\PublicLocale;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class SiteSettingsService
{
    public const CACHE_KEY = 'site_settings.current.v2';

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
            'branding_settings' => $record->branding_settings ?? [],
            'static_export_settings' => $record->static_export_settings ?? [],
            'publishing_state' => $record->publishing_state ?? [],
            'admin_state' => $record->admin_state ?? [],
        ]);
    }

    protected function defaultPayload(string $locale): array
    {
        $site = config('site');
        $localized = $this->localizedDefaults($locale);
        $contact = $site['contact'] ?? [];
        $profiles = array_values(array_filter($site['author']['same_as'] ?? []));

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
                'default_theme' => 'morning',
                'gradient_angle' => 132,
                'surface_blur' => 18,
                'line_thickness' => 1,
            ],
            'branding_settings' => [
                'asset_mode' => 'uploaded',
                'uploaded_asset_path' => '/images/contact-avatar.png',
                'fallback_variant' => 'monogram_circle',
                'fallback_label' => 'IR',
                'fallback_subtitle' => 'Sidewalk Studio',
                'active_alt' => 'Ismael Rodmacq portrait',
            ],
            'static_export_settings' => [
                'static_mode_enabled' => true,
                'github_pages_enabled' => true,
                'preprod_mode_enabled' => false,
                'export_base_path' => '/sidewalk-studio/',
                'export_output_path' => 'dist/static-preview',
            ],
            'publishing_state' => [
                'rebuild_required' => false,
                'last_rebuilt_at' => null,
                'last_change_summary' => null,
            ],
            'admin_state' => [
                'onboarding_completed_at' => null,
                'primary_operator_email' => null,
            ],
        ];
    }

    public function markRebuildRequired(string $summary): SiteSettings
    {
        $payload = $this->current()->toPersistenceArray();
        $payload['publishing_state'] = [
            'rebuild_required' => true,
            'last_rebuilt_at' => $payload['publishing_state']['last_rebuilt_at'] ?? null,
            'last_change_summary' => $summary,
        ];

        $this->store($this->hydrate($payload));

        return $this->refresh();
    }

    public function markRebuilt(?string $summary = null): SiteSettings
    {
        $payload = $this->current()->toPersistenceArray();
        $payload['publishing_state'] = [
            'rebuild_required' => false,
            'last_rebuilt_at' => CarbonImmutable::now()->toIso8601String(),
            'last_change_summary' => $summary,
        ];

        $this->store($this->hydrate($payload));

        return $this->refresh();
    }

    public function completeOnboarding(string $email): SiteSettings
    {
        $payload = $this->current()->toPersistenceArray();
        $payload['admin_state'] = [
            'onboarding_completed_at' => CarbonImmutable::now()->toIso8601String(),
            'primary_operator_email' => $email,
        ];

        $this->store($this->hydrate($payload));

        return $this->refresh();
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
