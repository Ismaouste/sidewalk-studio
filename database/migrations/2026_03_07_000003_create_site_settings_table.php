<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->unsignedTinyInteger('id')->primary();
            $table->json('site_identity');
            $table->json('contact_details');
            $table->json('social_links');
            $table->json('seo_defaults');
            $table->json('consent_copy');
            $table->json('feature_toggles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
