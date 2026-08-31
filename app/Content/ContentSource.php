<?php

namespace App\Content;

/**
 * Which of the two content sources wins when both hold the same item.
 *
 * The site has read Markdown first since it was built, with the database
 * merged underneath and two tests pinning the direction. That was a
 * deliberate decision — versioned content could not be quietly overwritten by
 * a stale row — and it had one consequence nobody wanted: the admin has been
 * saving page and publication edits that the public site ignores.
 *
 * Reversing it is the riskiest step in this spec, so it arrives as a setting
 * with a default rather than as an edit spread across two repositories. That
 * buys three things:
 *
 * - the fidelity test can render the same route both ways in one process and
 *   compare, which is the only way to prove seeding lost nothing;
 * - the reversal itself is a one-line change to a default, reviewable on its
 *   own;
 * - a deployment that goes wrong can be put back with an environment
 *   variable rather than a release.
 */
enum ContentSource: string
{
    /**
     * Markdown wins. Versioned content is authoritative and the database is a
     * merge underneath it.
     */
    case Files = 'files';

    /**
     * The database wins. Markdown becomes the seed format: it is what
     * `migrate:fresh --seed` reads, and what an operator reverts a page to.
     */
    case Database = 'database';

    public static function current(): self
    {
        return self::tryFrom((string) config('site.content_source')) ?? self::Files;
    }

    public static function databaseWins(): bool
    {
        return self::current() === self::Database;
    }
}
