<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'router_id',
        'voucher_id',
        'provider',
        'reference',
        'voucher_code',
        'phone',
        'amount',
        'status',
        'result_description',
        'payload',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'payload' => 'array',
    ];

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }
}
