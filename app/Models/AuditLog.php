<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Append-only log (the database role can only INSERT and SELECT here). */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'action', 'subject_type', 'subject_id', 'ip', 'user_agent'];

    /** Never throws: a logging problem must not break the page. */
    public static function record(string $action, ?Model $subject = null): void
    {
        try {
            static::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'subject_type' => $subject ? class_basename($subject) : null,
                'subject_id' => $subject?->getKey(),
                'ip' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
