<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HotspotUser extends Model
{
    protected $fillable = [
        'router_id',
        'profile_id',
        'username',
        'password',
        'active',
        'last_login_at',
        'total_time_seconds',
        'total_data_mb',
    ];

    protected $casts = [
        'active'         => 'boolean',
        'last_login_at'  => 'datetime',
    ];

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }
}
