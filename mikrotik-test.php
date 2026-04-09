<?php

require __DIR__.'/vendor/autoload.php';

use RouterOS\Client;
use RouterOS\Query;

$client = new Client([
    'host' => '192.168.1.10',
    'user' => 'billing',
    'pass' => 'NewStrongPass123',
    'port' => 8728,
]);

$username = 'TESTAPI1';

$print = new Query('/ip/hotspot/user/print');
$print->where('name', $username);
$resp = $client->query($print)->read();
var_dump($resp);

$add = new Query('/ip/hotspot/user/add');
$add->equal('name', $username)
    ->equal('password', $username)
    ->equal('profile', '1 Hour 5M')
    ->equal('disabled', 'no');

$resp2 = $client->query($add)->read();
var_dump($resp2);
