<?php

namespace Database\Seeders;

use App\Services\SiteSettingsService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(SiteSettingsService $siteSettings): void
    {
        $snapshotPath = database_path('seeders/data/site-settings.json');

        if (! File::exists($snapshotPath)) {
            $siteSettings->bootstrap();

            return;
        }

        $payload = json_decode((string) File::get($snapshotPath), true);

        if (! is_array($payload)) {
            throw new \RuntimeException("Invalid site settings snapshot [{$snapshotPath}].");
        }

        $siteSettings->store($siteSettings->hydrate($payload));
        $siteSettings->refresh();
    }
}
