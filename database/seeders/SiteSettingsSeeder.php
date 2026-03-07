<?php

namespace Database\Seeders;

use App\Services\SiteSettingsService;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(SiteSettingsService $siteSettings): void
    {
        $siteSettings->bootstrap();
    }
}
