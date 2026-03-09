<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'page_key',
        'locale',
        'title',
        'description',
        'seo_title',
        'seo_description',
        'robots',
        'canonical_url',
        'open_graph_image',
        'payload',
        'source_path',
        'source_driver',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
