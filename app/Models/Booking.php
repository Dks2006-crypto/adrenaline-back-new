<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'class_id',
        'trainer_id',
        'status',
        'cancelled_at',
        'note'
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'class_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class);
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
