<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'router_id',
        'profile_id',
        'status',           // 'new','reserved','used','expired','disabled'
        'expires_at',
        'face_value',
        'customer_phone',
        'hotspot_user_id',
        'synced_to_mikrotik',
        'time_limit_seconds',
        'data_limit_mb',
        'total_time_seconds',
        'total_data_mb',
    ];

    protected $casts = [
        'expires_at'         => 'datetime',
        'used_at'            => 'datetime',
        'synced_to_mikrotik' => 'boolean',
    ];

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function hotspotUser(): BelongsTo
    {
        return $this->belongsTo(HotspotUser::class, 'hotspot_user_id');
    }
}
