<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserLocationController extends Controller
{
    /**
     * Update the current user's location.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $request->user()->update([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'location_updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Clear the current user's location (when they close the tab).
     */
    public function clear(Request $request)
    {
        $request->user()->update([
            'latitude' => null,
            'longitude' => null,
            'location_updated_at' => null,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get all active user locations (updated within the last 15 seconds).
     */
    public function index(Request $request)
    {
        $activeUsers = User::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('location_updated_at', '>=', Carbon::now()->subSeconds(15))
            ->where('id', '!=', $request->user()->id) // Exclude current user
            ->get(['id', 'name', 'latitude', 'longitude', 'location_updated_at']);

        return response()->json($activeUsers);
    }
}
