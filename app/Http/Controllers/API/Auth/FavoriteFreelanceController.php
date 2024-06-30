<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\JObsForFreelancers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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

public function searchFavoriteForFreelance(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Search field is required',
            'error' => $validator->errors()
        ], 422);
    }

    $user = Auth::id();
    // $freelance_favorites = $user->freelance_favorites()->where('j_obs_for_freelancers_id', $request->j_obs_for_freelancers_id)->first();
    //j_obs_for_freelancers_id freelance_favorites
    $favorites = DB::table('j_obs_for_freelancers')
        ->join('freelance_favorites', 'j_obs_for_freelancers.id', '=', 'freelance_favorites.j_obs_for_freelancers_id')
            ->where('j_obs_for_freelancers.title', 'like', '%' . $request->title . '%')
            ->where('freelance_favorites.user_id', $user)
            ->get([
                "j_obs_for_freelancers.id",
                "j_obs_for_freelancers.title",
                "j_obs_for_freelancers.category_id"
            ]);


    if (count($favorites)!=0) {
        // $job = JObsForFreelancers::find($freelance_favorites->j_obs_for_freelancers_id);

        return response()->json([
            'data' => $favorites,
            'message' => 'Favorite freelance job found'
        ], 200);
    } else {
        return response()->json([
            'data' => null,
            'message' => 'No favorite freelance job found with the given ID'
        ], 404);
    }
}
}
