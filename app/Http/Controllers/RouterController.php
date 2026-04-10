<?php

namespace App\Http\Controllers;

use App\Models\Router;
use Illuminate\Http\Request;

class RouterController extends Controller
{
    public function index()
{
    $routers = Router::orderBy('name')->paginate(15);
    return view('routers.index', compact('routers'));
}

    public function create()
    {
        return view('routers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'location'     => 'nullable|string|max:255',
            'ip_address'   => 'required|string|max:255',
            'api_port'     => 'required|integer|min:1|max:65535',
            'api_username' => 'required|string|max:255',
            'api_password' => 'required|string|max:255',
            'enabled'      => 'nullable',
        ]);

        $data['enabled'] = $request->has('enabled');

        Router::create($data);

        return redirect()->route('routers.index')
            ->with('status', 'Router created.');
    }

    public function edit(Router $router)
    {
        return view('routers.edit', compact('router'));
    }

    public function update(Request $request, Router $router)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'location'     => 'nullable|string|max:255',
            'ip_address'   => 'required|string|max:255',
            'api_port'     => 'required|integer|min:1|max:65535',
            'api_username' => 'required|string|max:255',
            'api_password' => 'required|string|max:255',
            'enabled'      => 'nullable',
        ]);

        $data['enabled'] = $request->has('enabled');

        $router->update($data);

        return redirect()->route('routers.index')
            ->with('status', 'Router updated.');
    }

    public function destroy(Router $router)
    {
        $router->delete();

        return redirect()->route('routers.index')
            ->with('status', 'Router deleted.');
    }
}
