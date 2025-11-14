<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_percent',
        'valid_from',
        'valid_to',
        'usage_limit',
        'used_count'
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
    ];

    public function isValid(): bool
    {
        $now = now();

        return $this->used_count < ($this->usage_limit ?? PHP_INT_MAX)
            && (! $this->valid_from || $this->valid_from <= $now)
            && (! $this->valid_to || $this->valid_to >= $now);
    }
}
