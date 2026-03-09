<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->json('theme_settings')->nullable()->after('feature_toggles');
            $table->json('static_export_settings')->nullable()->after('theme_settings');
            $table->json('publishing_state')->nullable()->after('static_export_settings');
            $table->json('admin_state')->nullable()->after('publishing_state');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'theme_settings',
                'static_export_settings',
                'publishing_state',
                'admin_state',
            ]);
        });
    }
};
