<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $table = 'forms';

    protected $fillable = [
        'service_id',
        'trainer_id',
        'branch_id',
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

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'class_id');
    }

    public function availableSlots()
    {
        $booked = $this->bookings()->where('status', 'confirmed')->count();
        return $this->capacity - $booked;
    }
}
