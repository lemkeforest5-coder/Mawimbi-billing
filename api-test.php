<?php
require 'vendor/autoload.php';

use RouterOS\Client;

try {
    $client = new Client([
        'host' => '10.10.10.1',
        'user' => 'billing',
        'pass' => 'NewStrongPass123',
        'port' => 8728,
        'timeout' => 3,
    ]);
    echo "API LOGIN OK\n";
} catch (\Throwable $e) {
    echo "API LOGIN ERROR: " . $e->getMessage() . "\n";
}
