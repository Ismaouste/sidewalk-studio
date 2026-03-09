<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->json('branding_settings')->nullable()->after('theme_settings');
        });

        Schema::create('loader_quotes', function (Blueprint $table): void {
            $table->id();
            $table->text('text');
            $table->string('type', 16)->default('message');
            $table->string('author', 160)->nullable();
            $table->string('locale', 8);
            $table->boolean('is_active')->default(true);
            $table->string('theme_target', 32)->nullable();
            $table->unsignedInteger('weight')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loader_quotes');

        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn('branding_settings');
        });
    }
};
