<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pairs a publication with its other-language self.
 *
 * Until now nothing did, and nothing needed to: each locale is a directory
 * under `resources/content/`, and the directory was the link. Six of the
 * eleven journal entries and two of the four case studies have a different
 * slug in French, so the pairing was never derivable from the slug — it was
 * derivable from the filesystem, and only from the filesystem.
 *
 * Rows in one table have no filesystem. The moment the database becomes
 * authoritative, "the French version of this article" stops having any
 * expression in the data, and the admin cannot offer it. So the column lands
 * before the precedence reversal rather than with it.
 *
 * It is nullable and backfilled from the slug, because a publication that
 * exists in one language only is a legitimate state — the English fallback
 * entries are exactly that — and such a row simply pairs with nothing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table): void {
            $table->string('translation_key', 160)->nullable()->after('slug');
            $table->index(['type', 'translation_key']);
        });

        DB::table('publications')
            ->whereNull('translation_key')
            ->update(['translation_key' => DB::raw('slug')]);
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table): void {
            $table->dropIndex(['type', 'translation_key']);
            $table->dropColumn('translation_key');
        });
    }
};
