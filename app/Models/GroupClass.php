<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupClass extends Model
{
    protected $fillable = [
        'title',
        'description',
        'trainer_id',
        'service_id',
        'starts_at',
        'ends_at',
        'capacity',
        'currency',
        'active'
    ];

    protected $casts = [
        'starts_at' => 'datetime:Y-m-d H:i:s',
        'ends_at'   => 'datetime:Y-m-d H:i:s',
        'active' => 'boolean',
    ];



    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'group_class_id');
    }

    public function confirmedBookings(): HasMany
    {
        return $this->bookings()->where('status', \App\Models\Booking::STATUS_CONFIRMED);
    }

    public function availableSlots(): int
    {
        $booked = $this->confirmedBookings()->count();
        return max(0, $this->capacity - $booked);
    }
}
