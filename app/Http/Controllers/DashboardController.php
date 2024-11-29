<?php

namespace App\Http\Controllers;
use App\Models\Users;
use App\Models\StorageProvider;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get authenticated user id
            $userId = Auth::id(); 

            // Get current team
            $team = Auth::user()->currentTeam;

            // Get storage providers by user id and team id
            $storageProviders = StorageProvider::where('user_id', $userId)
                ->where('team_id', $team->id)
                ->get();

            $count = $storageProviders->count();

            $drivers = $storageProviders->map(function ($provider) {
                $config = $provider->configuration;
                return $config['driver'] ?? null;
            })->filter()
                ->unique()
                ->toArray();

                // uppercase the drivers
                $drivers = array_map('ucfirst', $drivers);

                // get the count of the teams the user belongs to
                $teamCount = auth()->user()->allTeams()->count();
    
            return Inertia::render('Dashboard', [
                'storageProviderCount' => $count,
                'drivers' => $drivers,
                'teamCount' => $teamCount
            ]);
        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Log::error('Dashboard error: ' . $e->getMessage());

            // Handle errors gracefully, e.g., log the error and display a user-friendly message
            return Inertia::render('Dashboard', [
                'error' => 'An error occurred while fetching storage providers.',
                'errorDetails' => $e->getMessage() // Optional: for more detailed error tracking
            ]);
        }
    }
}