<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExperienceEntry extends Model
{
    /** The three families the experience record has always been split into. */
    public const KINDS = ['professional', 'side_project', 'associative'];

    protected $fillable = [
        'translation_key',
        'locale',
        'kind',
        'organisation',
        'role',
        'started_on',
        'ended_on',
        'date_label',
        'summary',
        'paragraphs',
        'detail_groups',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'started_on' => 'date',
            'ended_on' => 'date',
            'paragraphs' => 'array',
            'detail_groups' => 'array',
            'position' => 'integer',
        ];
    }

    /**
     * Newest first, which is the only order a chronology is ever read in.
     *
     * A row with no start date sorts last rather than first. `Avant 2023`
     * means "before everything dated here", and a null that floated to the top
     * would put the vaguest entry above the current job. `position` breaks
     * ties inside a single date so an operator keeps the last word, and `id`
     * breaks ties inside a single position so the order never depends on how
     * the database happens to return rows.
     */
    public function scopeInChronologicalOrder(Builder $query): Builder
    {
        return $query
            ->orderByRaw('started_on IS NULL')
            ->orderByDesc('started_on')
            ->orderBy('position')
            ->orderBy('id');
    }
}
