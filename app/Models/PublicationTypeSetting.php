<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationTypeSetting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'type',
        'cta_label',
        'cta_target',
        'accent_color',
    ];
}
