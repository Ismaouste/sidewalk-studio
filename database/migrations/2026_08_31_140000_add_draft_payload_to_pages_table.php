<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Where an unpublished edit waits to be looked at.
 *
 * The preview is the real route rendered with the draft, because for a site
 * whose value is how it looks, an in-editor mock is worse than nothing. That
 * needs the draft to survive one redirect and one fresh request.
 *
 * The session would have been the obvious place and is the wrong one here:
 * sessions are cookie-backed, a cookie is capped at 4KB, and the experience
 * record alone carries 125 fields. A draft stored there would have been
 * silently dropped for exactly the pages that most need previewing — the
 * same failure that swallowed a validation message earlier in this branch,
 * which is how the limit came up.
 *
 * A column also gives the draft a life longer than a browser session: an
 * operator can leave a page half-rewritten and come back to it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->json('draft_payload')->nullable()->after('payload');
            $table->timestamp('draft_saved_at')->nullable()->after('draft_payload');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropColumn(['draft_payload', 'draft_saved_at']);
        });
    }
};
