<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\User;

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

    // Check if the user already has the job in their favorites
    if ($user->favorites()->where('job_id', $request->job_id)->exists()) {
        return response()->json(['error' => 'Job is already in favorites'], 400);
    }

    // Create a new favorite record
    $user->favorites()->create([
        'job_id' => $request->job_id,
    ]);

    // Return a success message
    return response()->json(['message' => 'Job added to favorites'], 201);
}
public function destroy(Request $request)
{
    // Retrieve the authenticated user
    $user = auth()->user();

    // Attempt to delete the favorite record
    $user->favorites()->where('job_id', $request->job_id)->delete();

    // Return a success message
    return response()->json(['message' => 'Job removed from favorites'], 200);
}

public function getAllFavorites(Request $request, $id)
{
    // Retrieve the user using the $id
    $user = User::findOrFail($id);

    // Load the favorite jobs for the user, including the related job data
    $favoriteJobs = $user->favorites()
                         ->get();
    // Return the favorite jobs as a JSON response
    return response()->json($favoriteJobs);
}
}
