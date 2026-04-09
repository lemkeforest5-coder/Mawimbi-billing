<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'code',
        'name',
        'price_kes',
        'duration_minutes',
        'duration_days',
        'max_devices',
        'rate_limit',
        'data_cap_mb',
    ];

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
