<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $table = 'forms';

    protected $fillable = [
        'service_id',
        'trainer_id',
        'starts_at',
        'ends_at',
        'capacity'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'class_id');
    }

    public function confirmedBookings()
    {
        return $this->bookings()->where('status', \App\Models\Booking::STATUS_CONFIRMED);
    }

    public function availableSlots()
    {
        $booked = $this->confirmedBookings()->count();
        return max(0, $this->capacity - $booked);
    }
}
