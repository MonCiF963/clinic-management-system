<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Weekly working hours of a doctor. weekday: 0 (Sunday) to 6 (Saturday). */
class Availability extends Model
{
    public $timestamps = false;

    protected $fillable = ['doctor_id', 'weekday', 'start_time', 'end_time'];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
