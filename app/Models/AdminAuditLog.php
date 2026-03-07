<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'actor_type',
        'actor_id',
        'actor_name',
        'actor_email',
        'action',
        'subject',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'summary' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
