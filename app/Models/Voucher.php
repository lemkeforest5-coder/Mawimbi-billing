<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'router_id',
        'profile_id',
        'code',
        'face_value',
        'time_limit_seconds',
        'data_limit_mb',
        'price',
        'total_time_seconds',
        'total_data_mb',
        'status',
        'synced_to_mikrotik',
        'expires_at',
        'used_at',
        'customer_phone',
        'hotspot_user_id',
    ];

    protected $casts = [
        'expires_at'         => 'datetime',
        'used_at'            => 'datetime',
        'total_time_seconds' => 'integer',
        'total_data_mb'      => 'float',
    ];

    public function payment()
    {
        return $this->hasOne(\App\Models\Payment::class);
    }

    protected static function booted()
    {
        static::creating(function (Voucher $voucher) {
            $profile = $voucher->profile ?? Profile::find($voucher->profile_id);

            if (! $profile) {
                return;
            }

            // Time from profile minutes → seconds
            if (is_null($voucher->time_limit_seconds) && ! is_null($profile->time_limit_minutes)) {
                $voucher->time_limit_seconds = $profile->time_limit_minutes * 60;
            }

            // Data limit
            if (is_null($voucher->data_limit_mb) && ! is_null($profile->data_limit_mb)) {
                $voucher->data_limit_mb = $profile->data_limit_mb;
            }

            // Price
            if (is_null($voucher->price) && ! is_null($profile->price)) {
                $voucher->price = $profile->price;
            }

            // Optional: face_value same as price
            if (is_null($voucher->face_value) && ! is_null($voucher->price)) {
                $voucher->face_value = $voucher->price;
            }

            // Default status
            if (is_null($voucher->status)) {
                $voucher->status = 'new';
            }
        });
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function getTotalTimeHumanAttribute(): string
    {
        $seconds = $this->total_time_seconds ?? 0;

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        return sprintf('%02dh %02dm', $hours, $minutes);
    }

    public function getRemainingTimeSecondsAttribute(): ?int
    {
        $profile = $this->profile;

        if (! $profile || is_null($profile->time_limit_minutes)) {
            return null;
        }

        $limitSeconds = $profile->time_limit_minutes * 60;
        $used = $this->total_time_seconds ?? 0;

        return max($limitSeconds - $used, 0);
    }

    public function getRemainingDataMbAttribute(): ?float
    {
        $profile = $this->profile;

        if (! $profile || is_null($profile->data_limit_mb)) {
            return null;
        }

        $limitMb = $profile->data_limit_mb;
        $used = $this->total_data_mb ?? 0.0;

        return max($limitMb - $used, 0.0);
    }
}
