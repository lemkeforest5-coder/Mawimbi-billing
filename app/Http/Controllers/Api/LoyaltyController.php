<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->query('user');

        if (! $user) {
            return response()->json([
                'ok'      => false,
                'message' => 'Missing user parameter.',
            ], 400);
        }

        // TODO: replace with real Mawimbi DB logic later
        $sessions = 0;   // number of paid sessions this month
        $target   = 20;   // target to get free daily pass

        return response()->json([
            'ok'       => true,
            'user'     => $user,
            'sessions' => $sessions,
            'target'   => $target,
        ]);
    }
}
