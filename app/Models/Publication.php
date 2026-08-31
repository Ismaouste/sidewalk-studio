<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    protected $fillable = [
        'type',
        'locale',
        'title',
        'slug',
        'translation_key',
        'summary',
        'body_markdown',
        'status',
        'published_at',
        'updated_at_publication',
        'tags',
        'seo_title',
        'seo_description',
        'robots',
        'canonical_url',
        'featured_image',
        'featured_image_alt',
        'open_graph_image',
        'featured_video',
        'category',
        'accent_tone',
        'metadata',
        'source_path',
        'source_driver',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'updated_at_publication' => 'date',
            'tags' => 'array',
            'metadata' => 'array',
        ];
    }
}
