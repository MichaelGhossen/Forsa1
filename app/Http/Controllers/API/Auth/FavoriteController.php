<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    $user = Auth::id();

    // Fetch the favorite jobs for the user
    $favorites = DB::table('jobs')
        ->join('favorites', 'jobs.id', '=', 'favorites.job_id')
        ->where('favorites.user_id', $user)
        ->select(
            'favorites.id as favorite_id',
            'jobs.id as job_id',
            'jobs.title',
            'jobs.category_id'
        )
        ->get();

    if ($favorites->isNotEmpty()) {
        $data = $favorites->map(function ($favorite) {
            return [
                'job_id' => $favorite->job_id,
                'favorite_id' => $favorite->favorite_id,
                'title' => $favorite->title,
                'category_id' => $favorite->category_id,
            ];
        });

        return response()->json([
            'data' => $data,
            'message' => 'Jobs found in favorites',
        ], 200);
    }

    return response()->json([
        'data' => [],
        'message' => 'No jobs found in favorites',
    ], 200);
}
    public function searchJobInFavorites(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Job title is required',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::id();
        $favorites = DB::table('jobs')
        ->join('favorites', 'jobs.id', '=', 'favorites.job_id')
            ->where('jobs.title', 'like', '%' . $request->title . '%')
            ->where('favorites.user_id', $user)
            ->get([
                "jobs.id",
                "jobs.title",
                "jobs.category_id"
            ]);
        if ($favorites->isNotEmpty()) {
            $data = $favorites->map(
                function ($favorite) {});
            return response()->json([
                'data' => $favorites
            ]);

            return response()->json([
                'data' => $data,
                'message' => 'Jobs found in favorites',
            ], 200);
        } else {
            return response()->json([
                'data' => null,
                'message' => 'No job found in favorites matching the search criteria',
            ], 404);
        }
    }
}
