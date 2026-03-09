<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 32);
            $table->string('locale', 8);
            $table->string('title', 160);
            $table->string('slug', 160);
            $table->string('summary', 320);
            $table->longText('body_markdown')->default('');
            $table->string('status', 32)->default('draft');
            $table->date('published_at')->nullable();
            $table->date('updated_at_publication')->nullable();
            $table->json('tags')->nullable();
            $table->string('seo_title', 160);
            $table->string('seo_description', 500);
            $table->string('robots', 40)->default('index,follow');
            $table->string('canonical_url', 255)->nullable();
            $table->string('featured_image', 255)->nullable();
            $table->string('featured_image_alt', 255)->nullable();
            $table->string('open_graph_image', 255)->nullable();
            $table->string('featured_video', 255)->nullable();
            $table->string('category', 64)->nullable();
            $table->string('accent_tone', 64)->nullable();
            $table->json('metadata')->nullable();
            $table->string('source_path')->nullable();
            $table->string('source_driver', 32)->default('database');
            $table->timestamps();

            $table->unique(['type', 'locale', 'slug']);
        });

        Schema::create('pages', function (Blueprint $table): void {
            $table->id();
            $table->string('page_key', 80);
            $table->string('locale', 8);
            $table->string('title', 160)->nullable();
            $table->string('description', 500)->nullable();
            $table->string('seo_title', 160);
            $table->string('seo_description', 500);
            $table->string('robots', 40)->default('index,follow');
            $table->string('canonical_url', 255)->nullable();
            $table->string('open_graph_image', 255)->nullable();
            $table->json('payload')->nullable();
            $table->string('source_path')->nullable();
            $table->string('source_driver', 32)->default('database');
            $table->timestamps();

            $table->unique(['page_key', 'locale']);
        });

        Schema::create('publication_type_settings', function (Blueprint $table): void {
            $table->string('type', 32)->primary();
            $table->string('cta_label', 120);
            $table->string('cta_target', 255);
            $table->string('accent_color', 32)->default('dominant');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_type_settings');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('publications');
    }
};
