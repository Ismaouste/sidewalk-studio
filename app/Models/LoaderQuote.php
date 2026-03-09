<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaderQuote extends Model
{
    protected $fillable = [
        'text',
        'type',
        'author',
        'locale',
        'is_active',
        'theme_target',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'weight' => 'integer',
        ];
    }
}
