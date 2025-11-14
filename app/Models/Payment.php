<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'membership_id', 'amount_cents',
        'currency', 'provider', 'provider_payment_id', 'status'
    ];

    protected $casts = [
        'amount_cents' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
