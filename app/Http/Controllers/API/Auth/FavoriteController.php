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
        $user = User::findOrFail($id);

        // Load the favorite jobs for the user, including the related job data
        $favoriteJobs = $user->favorites()
            ->get();
        // Return the favorite jobs as a JSON response
        return response()->json($favoriteJobs);
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
        // $user->favorites()
        //     ->whereHas('job', function ($query) use ($request) {
        //         $query->where('title', 'like', '%' . $request->title . '%');
        //     })
        //     ->with('job')
        //     ->get();

        if ($favorites->isNotEmpty()) {
            $data = $favorites->map(
                function ($favorite) {
                    // return [
                    //     'id' => $favorite->id,
                    //     'job_id' => $favorite->job_id,
                    //     'title' => $favorite->job->title,
                    // ];
                }
            );
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
