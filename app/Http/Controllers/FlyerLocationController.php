<?php

namespace App\Http\Controllers;

use App\Models\FlyerLocation;
use Illuminate\Http\Request;

class FlyerLocationController extends Controller
{
    public function index()
    {
        return FlyerLocation::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $flyerLocation = FlyerLocation::create($validated);

        return response()->json($flyerLocation, 201);
    }

    public function show(FlyerLocation $flyerLocation)
    {
        return $flyerLocation;
    }

    public function update(Request $request, FlyerLocation $flyerLocation)
    {
        $validated = $request->validate([
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'sometimes|required|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $flyerLocation->update($validated);

        return response()->json($flyerLocation);
    }

    public function destroy(FlyerLocation $flyerLocation)
    {
        $flyerLocation->delete();

        return response()->json(null, 204);
    }
}
