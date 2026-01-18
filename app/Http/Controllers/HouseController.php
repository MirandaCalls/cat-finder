<?php

namespace App\Http\Controllers;

use App\Models\House;
use Illuminate\Http\Request;

class HouseController extends Controller
{
    public function index()
    {
        return House::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            'flyer_left' => 'boolean',
            'talked_to_owners' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $house = House::create($validated);

        return response()->json($house, 201);
    }

    public function show(House $house)
    {
        return $house;
    }

    public function update(Request $request, House $house)
    {
        $validated = $request->validate([
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'sometimes|required|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            'flyer_left' => 'boolean',
            'talked_to_owners' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $house->update($validated);

        return response()->json($house);
    }

    public function destroy(House $house)
    {
        $house->delete();

        return response()->json(null, 204);
    }
}
