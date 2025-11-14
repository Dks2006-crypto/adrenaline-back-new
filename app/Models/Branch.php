<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'address',
        'timezone',
        'contact_phone'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Form::class);
    }
}
