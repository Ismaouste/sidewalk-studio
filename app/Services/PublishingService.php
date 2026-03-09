<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;

class PublishingService
{
    public function __construct(
        protected SiteSettingsService $siteSettings,
    ) {}

    /**
     * @return array{ran: bool, output: string}
     */
    public function rebuild(): array
    {
        $settings = $this->siteSettings->current();

        if (! $settings->publishingState->rebuildRequired) {
            return [
                'ran' => false,
                'output' => 'No rebuild was needed.',
            ];
        }

        Artisan::call('optimize:clear');

        $output = Artisan::output();
        $ran = false;

        if ($settings->staticExportSettings->staticModeEnabled) {
            $ran = true;

            Artisan::call('site:export-static-preview', [
                '--locales' => 'fr,en',
                '--output' => $settings->staticExportSettings->exportOutputPath,
                '--base' => $settings->staticExportSettings->exportBasePath,
            ]);

            $output .= Artisan::output();
        }

        $this->siteSettings->markRebuilt(
            $ran
                ? 'Application caches cleared and static export regenerated.'
                : 'Application caches cleared. Static export mode is disabled.',
        );

        return [
            'ran' => $ran,
            'output' => trim($output),
        ];
    }
}
