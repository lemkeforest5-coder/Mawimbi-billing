<?php

namespace App\Services;

use App\Models\Router;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Query;

class MikrotikService
{
    /**
     * Create or enable a hotspot user on the given router.
     *
     * @throws \Exception
     */
    public function createOrEnableHotspotUser(
        Router $router,
        string $username,
        string $routerProfileName
    ): void {
        Log::info('MikrotikService: connecting', [
            'host'    => $router->ip_address,
            'port'    => $router->api_port ?? 8728,
            'user'    => $router->api_username,
            'name'    => $username,
            'profile' => $routerProfileName,
        ]);

        $client = new Client([
            'host' => $router->ip_address,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port ?? 8728,
        ]);

        // 1) Try to find existing user
        $printQuery = new Query('/ip/hotspot/user/print');
        $printQuery->where('name', $username);

        $response = $client->query($printQuery)->read();
        Log::info('MikrotikService: print response', ['response' => $response]);

        $userExists = false;
        $userId = null;

        foreach ($response as $item) {
            if (isset($item['name']) && $item['name'] === $username) {
                $userExists = true;
                $userId = $item['.id'] ?? null;
                break;
            }
        }

        if ($userExists && ! empty($userId)) {
            // 2a) User exists: update profile and enable
            $setQuery = new Query('/ip/hotspot/user/set');
            $setQuery->equal('.id', $userId)
                     ->equal('profile', $routerProfileName)
                     ->equal('disabled', 'no');

            $client->query($setQuery)->read();
            Log::info('MikrotikService: set result');
        } else {
            // 2b) User does not exist: create new hotspot user
            $addQuery = new Query('/ip/hotspot/user/add');
            $addQuery->equal('name', $username)
                     ->equal('password', $username)
                     ->equal('profile', $routerProfileName)
                     ->equal('disabled', 'no');

            $client->query($addQuery)->read();
            Log::info('MikrotikService: add result');
        }
    }

    /**
     * Disable a hotspot user on the given router.
     */
    public function disableHotspotUser(Router $router, string $username): void
    {
        $client = new Client([
            'host' => $router->ip_address,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port ?? 8728,
        ]);

        $printQuery = new Query('/ip/hotspot/user/print');
        $printQuery->where('name', $username);

        $response = $client->query($printQuery)->read();

        $userId = $response[0]['.id'] ?? null;

        if (! $userId) {
            Log::info('MikrotikService: disableHotspotUser - user not found', [
                'username' => $username,
                'router'   => $router->name,
            ]);

            return;
        }

        $setQuery = new Query('/ip/hotspot/user/set');
        $setQuery->equal('.id', $userId)
                 ->equal('disabled', 'yes');

        $client->query($setQuery)->read();

        Log::info('MikrotikService: hotspot user disabled', [
            'username' => $username,
            'router'   => $router->name,
        ]);
    }
}
