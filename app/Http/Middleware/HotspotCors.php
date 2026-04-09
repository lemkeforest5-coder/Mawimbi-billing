<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HotspotCors
{
    public function handle(Request $request, Closure $next)
    {
        // Handle preflight OPTIONS early
        if ($request->getMethod() === 'OPTIONS') {
            $response = response('', 204);
        } else {
            $response = $next($request);
        }

        $origin = $request->headers->get('Origin');

        $allowed = [
            'http://hotspot.local',
            'http://10.10.10.1',
        ];

        if ($origin && in_array($origin, $allowed, true)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Origin');

        return $response;
    }
}
