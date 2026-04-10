<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Router;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $profiles = Profile::with('router')
            ->orderBy('name')
            ->paginate(15);

        return view('profiles.index', compact('profiles'));
    }

    public function create()
    {
        $routers = Router::orderBy('name')->pluck('name', 'id');

        return view('profiles.create', compact('routers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'router_id'          => 'required|exists:routers,id',
            'name'               => 'required|string|max:255',
            'code'               => 'required|string|max:50|unique:profiles,code',
            'rate_limit'         => 'nullable|string|max:255',
            'time_limit_minutes' => 'nullable|integer|min:0',
            'data_limit_mb'      => 'nullable|integer|min:0',
            'price'              => 'required|numeric|min:0',
            'is_default'         => 'nullable|boolean',
        ]);

        $data['is_default'] = $request->has('is_default');

        Profile::create($data);

        return redirect()->route('profiles.index')
            ->with('status', 'Profile created.');
    }

    public function edit(Profile $profile)
    {
        $routers = Router::orderBy('name')->pluck('name', 'id');

        return view('profiles.edit', compact('profile', 'routers'));
    }

    public function update(Request $request, Profile $profile)
    {
        $data = $request->validate([
            'router_id'          => 'required|exists:routers,id',
            'name'               => 'required|string|max:255',
            'code'               => 'required|string|max:50|unique:profiles,code,' . $profile->id,
            'rate_limit'         => 'nullable|string|max:255',
            'time_limit_minutes' => 'nullable|integer|min:0',
            'data_limit_mb'      => 'nullable|integer|min:0',
            'price'              => 'required|numeric|min:0',
            'is_default'         => 'nullable|boolean',
        ]);

        $data['is_default'] = $request->has('is_default');

        $profile->update($data);

        return redirect()->route('profiles.index')
            ->with('status', 'Profile updated.');
    }

    public function destroy(Profile $profile)
    {
        $profile->delete();

        return redirect()->route('profiles.index')
            ->with('status', 'Profile deleted.');
    }
}
