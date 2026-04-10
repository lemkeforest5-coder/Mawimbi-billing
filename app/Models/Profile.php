<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'router_id',
        'name',
        'router_profile_name',
        'code',
        'rate_limit',
        'time_limit_minutes',
        'data_limit_mb',
        'price',
        'is_default',
    ];

    protected static function booted()
    {
        static::creating(function (Profile $profile) {
            // Load defaults from config/hotspot_profiles.php by name
            $defaults = config('hotspot_profiles.' . $profile->name);

            if (! $defaults) {
                return;
            }

            if (is_null($profile->time_limit_minutes) && array_key_exists('time_limit_minutes', $defaults)) {
                $profile->time_limit_minutes = $defaults['time_limit_minutes'];
            }

            if (is_null($profile->data_limit_mb) && array_key_exists('data_limit_mb', $defaults)) {
                $profile->data_limit_mb = $defaults['data_limit_mb'];
            }

            if (is_null($profile->price) && array_key_exists('price', $defaults)) {
                $profile->price = $defaults['price'];
            }
        });
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function router()
    {
        return $this->belongsTo(Router::class);
    }
}
