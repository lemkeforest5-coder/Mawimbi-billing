<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hotspot Profile Defaults
    |--------------------------------------------------------------------------
    |
    | One entry per package / profile. time_limit_minutes is the total time.
    | Prices are from your hotspot cards (Ksh).
    |
    */

    // KUMI: 40 Min – Ksh 10
    'KUMI' => [
        'time_limit_minutes' => 40,
        'data_limit_mb'      => null,   // unlimited data unless you enforce a cap
        'price'              => 10,
    ],

    // MBAO: 2 Hours – Ksh 20
    'MBAO' => [
        'time_limit_minutes' => 120,    // 2 * 60
        'data_limit_mb'      => null,
        'price'              => 20,
    ],

    // DAILY: 24 Hours – Ksh 80
    'DAILY' => [
        'time_limit_minutes' => 1440,   // 24 * 60
        'data_limit_mb'      => null,
        'price'              => 80,
    ],

    // WEEKLY SOLO: 7 Days – Ksh 280
    'WEEKLY SOLO' => [
        'time_limit_minutes' => 7 * 24 * 60,  // 10080
        'data_limit_mb'      => null,
        'price'              => 280,
    ],

    // MONTHLY SOLO: 30 Days – Ksh 720
    'MONTHLY SOLO' => [
        'time_limit_minutes' => 30 * 24 * 60, // 43200
        'data_limit_mb'      => null,
        'price'              => 720,
    ],

    // QTRLY FAMILY x4: 90 Days – Ksh 4200
    'QTRLY FAMILY x4' => [
        'time_limit_minutes' => 90 * 24 * 60, // 129600
        'data_limit_mb'      => null,
        'price'              => 4200,
    ],

];
