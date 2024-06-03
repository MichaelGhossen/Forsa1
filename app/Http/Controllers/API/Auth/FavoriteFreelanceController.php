<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FavoriteFreelanceController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request to ensure the j_obs_for_freelancers_id is present
        $validatedData = $request->validate([
            'j_obs_for_freelancers_id' => 'required|exists:j_obs_for_freelancers,id',
        ]);

        // Attempt to retrieve the authenticated user
        $user = auth()->user();

        // Check if the user is not null before proceeding
        if ($user === null) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check if the user already has the job for freelancers in their favorites
        if ($user->freelance_favorites()->where('j_obs_for_freelancers_id', $request->j_obs_for_freelancers_id)->exists()) {
            return response()->json(['error' => 'Job for freelancers is already in favorites'], 400);
        }

        // Attempt to attach the job to the user's favorites using the correct pivot table and column names
        $user->freelance_favorites()
        ->create([
            'j_obs_for_freelancers_id' => $request->j_obs_for_freelancers_id,
        ]);

        // Return a success message
        return response()->json(['message' => 'Job for freelancers added to favorites'], 201);
    }
    // j
      public function destroy(Request $request)
    {
        // Retrieve the authenticated user
        $user = auth()->user();

        // Attempt to detach the job from the user's favorites using the correct column name
        $favorite = $user->freelance_favorites()->
        where('j_obs_for_freelancers_id', $request->j_obs_for_freelancers_id)->delete();

        // Return a success message
        return response()->json(['message' => 'Job removed from favorites'], 200);
    }
    public function getAllFavorites(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $favoriteJobs = $user->freelance_favorites()
                             ->get();
        return response()->json($favoriteJobs);
    }
}
