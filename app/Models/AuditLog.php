<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Append-only log (the database role can only INSERT and SELECT here). */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'action', 'subject_type', 'subject_id', 'ip', 'user_agent'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
