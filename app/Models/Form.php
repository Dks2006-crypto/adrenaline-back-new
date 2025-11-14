<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'service_id',
        'trainer_id',
        'branch_id',
        'starts_at',
        'ends_at',
        'capacity',
        'recurrence_rule'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'class_id');
    }

    public function availableSlots(): int
    {
        return $this->capacity - $this->bookings()->where('status', 'confirmed')->count();
    }
}
