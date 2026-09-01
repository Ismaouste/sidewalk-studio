<?php

namespace App\Content\Schema;

use App\Models\ExperienceEntry;

/**
 * What one position held holds.
 *
 * Declared here rather than assembled in a controller for the same reason
 * every page is: the save path validates against this, and `/admin` generates
 * its form from it. A field cannot be added to an experience entry without an
 * input appearing for it, and the form cannot offer a field the save would
 * reject.
 *
 * `detail_groups` is the same shape the three section families already used
 * inside the page payload, and it has to stay that shape — the rows are read
 * back into that payload contract, and the public components have not moved.
 */
final class ExperienceSchemas
{
    /**
     * The columns an entry stores in its own right rather than in JSON.
     *
     * Read by both sides, declared once: the controller splits a save on it,
     * and the editor decides which half of the form a field belongs to. Two
     * copies of this list would be two ways for the form to offer a field the
     * save then misplaces.
     */
    public const RECORD_FIELDS = [
        'kind',
        'organisation',
        'role',
        'started_on',
        'ended_on',
        'date_label',
    ];

    public static function entry(): ContentSchema
    {
        return new ContentSchema('experience_entry', 'Experience entry', [
            Field::choice('kind', ExperienceEntry::KINDS, 'Family')
                ->withHelp('Which of the three chronologies this belongs to.'),
            Field::line('organisation', 'Organisation'),
            Field::line('role', 'Role'),

            /**
             * Both dates are optional, and that is the honest model rather
             * than a lenient one. An entry with no end is the one being
             * lived; an entry with no start is older than everything dated
             * and sorts last. Neither is a missing value to be nagged about.
             */
            Field::date('started_on', 'Started')
                ->optional()
                ->withHelp('Orders the chronology. Leave empty for history older than everything dated.'),
            Field::date('ended_on', 'Ended')
                ->optional()
                ->withHelp('Leave empty while the position is current.'),
            Field::line('date_label', 'Date label')
                ->optional()
                ->withHelp('Overrides the dates on the page. Clear it to let the dates speak.'),

            Field::text('summary', 'Summary'),
            Field::text('paragraphs', 'Paragraphs')->repeating(),
            Field::group('detail_groups', [
                Field::line('title', 'Title'),
                Field::line('pills', 'Pills')->optional()->repeating(),
                Field::text('items', 'Items')->repeating(),
            ], 'Detail groups')->repeating(itemLabel: 'title'),
        ]);
    }
}
