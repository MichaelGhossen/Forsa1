<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function store(Request $request)
{
    // Validate the request to ensure the job_id is present
    $validatedData = $request->validate([
        'job_id' => 'required|exists:jobs,id',
    ]);

    // Attempt to retrieve the authenticated user
    $user = auth()->user();

    // Check if the user is not null before proceeding
    if ($user === null) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Attempt to attach the job to the user's favorites
    $favorite = $user->favorites()->attach($request->job_id);

    // Return a success message
    return response()->json(['message' => 'Job added to favorites'], 201);
}


    public function destroy(Request $request)
    {
        // Retrieve the authenticated user
        $user = auth()->user();

        // Attempt to detach the job from the user's favorites
        $favorite = $user->favorites()->detach($request->job_id);

        // Return a success message
        return response()->json(['message' => 'Job removed from favorites'], 200);
    }
}
