<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Router extends Model
{
    protected $fillable = [
        'name',
        'location',
        'ip_address',
        'api_port',
        'api_username',
        'api_password',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function hotspotUsers(): HasMany
    {
        return $this->hasMany(HotspotUser::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
