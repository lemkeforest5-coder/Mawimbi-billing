<?php

require 'vendor/autoload.php';

use RouterOS\Client;

try {
    $client = new Client([
        'host' => '192.168.1.10',
        'user' => 'billing',
        'pass' => 'NewStrongPass123',
        'port' => 8728,
        'timeout' => 5,
    ]);

    echo "API LOGIN OK (new library)\n";
} catch (\Throwable $e) {
    echo "API LOGIN ERROR (new library): " . $e->getMessage() . "\n";
}
