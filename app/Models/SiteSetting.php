<?php

namespace App\Models;

use App\Services\SiteSettingsService;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    public const SINGLETON_ID = 1;

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'site_identity',
        'contact_details',
        'social_links',
        'seo_defaults',
        'consent_copy',
        'feature_toggles',
        'theme_settings',
        'static_export_settings',
        'publishing_state',
        'admin_state',
    ];

    protected function casts(): array
    {
        return [
            'site_identity' => 'array',
            'contact_details' => 'array',
            'social_links' => 'array',
            'seo_defaults' => 'array',
            'consent_copy' => 'array',
            'feature_toggles' => 'array',
            'theme_settings' => 'array',
            'static_export_settings' => 'array',
            'publishing_state' => 'array',
            'admin_state' => 'array',
        ];
    }

    protected static function booted(): void
    {
        $flushCache = static fn () => app(SiteSettingsService::class)->forget();

        static::saved($flushCache);
        static::deleted($flushCache);
    }
}
