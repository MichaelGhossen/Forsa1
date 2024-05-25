<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
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

        // Attempt to attach the job to the user's favorites using the correct column name
        $favorite = $user->freelance_favorites()->attach($request->j_obs_for_freelancers_id, ['user_id' => $user->id]);
        // Return a success message
        return response()->json(['message' => 'Job added to favorites'], 201);
    }

    public function destroy(Request $request)
    {
        // Retrieve the authenticated user
        $user = auth()->user();

        // Attempt to detach the job from the user's favorites using the correct column name
        $favorite = $user->freelance_favorites()->detach($request->j_obs_for_freelancers_id, ['user_id' => $user->id]);
        // Return a success message
        return response()->json(['message' => 'Job removed from favorites'], 200);
    }

}
