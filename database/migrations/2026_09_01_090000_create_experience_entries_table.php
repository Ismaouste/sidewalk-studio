<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per position held, per language.
 *
 * The experience record already described these, as three arrays of objects
 * inside the `experience` page payload — and it described the dates as prose.
 * `Développeur e-commerce — 2023-2026` was one string, and `Projects.vue`
 * recovered the two halves with `eyebrow.split(/\s+[—–-]\s+/)`. A role
 * containing a spaced dash would have been read as a date, the chronology was
 * ordered by the position of the item in the array, and nothing could ask when
 * a job started because nothing stored it.
 *
 * `started_on` is the ordering truth and `date_label` is the display
 * override, because some of this content is honestly imprecise: two of the
 * four entries say `Avant 2023`. A row with no start date sorts last and
 * shows its label. Seeding fills the label from the existing string for every
 * row, so the public page renders identically the day this lands; clearing a
 * label is what opts a row into computed dates.
 *
 * `translation_key` pairs a row with its other-language self, the same way
 * `publications` does — the organisation name is not a key, since `Jewely
 * E-commerce` and `Jewely Ecommerce` are the same job spelled twice.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_entries', function (Blueprint $table): void {
            $table->id();
            $table->string('translation_key');
            $table->string('locale', 5);
            $table->string('kind');
            $table->string('organisation');
            $table->string('role');
            $table->date('started_on')->nullable();
            $table->date('ended_on')->nullable();
            $table->string('date_label')->nullable();
            $table->text('summary');
            $table->json('paragraphs');
            $table->json('detail_groups');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['translation_key', 'locale']);
            $table->index(['locale', 'kind']);
            $table->index('started_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_entries');
    }
};
