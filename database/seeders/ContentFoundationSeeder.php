<?php

namespace Database\Seeders;

use App\Services\ContentImportService;
use Illuminate\Database\Seeder;

class ContentFoundationSeeder extends Seeder
{
    public function run(ContentImportService $importer): void
    {
        $importer->importAll();
    }
}
