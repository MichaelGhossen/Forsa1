<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Companies;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowController extends Controller
{
    public function showJobSeeker(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'job_seeker'|| $user->user_type !== 'admin') {
            return response()->json(['message' => 'Job seeker not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'image' => $user->image,
            'user_type' => $user->user_type,
        ]);
    }

    public function showJobOwner(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'job_owner'|| $user->user_type !== 'admin') {
            return response()->json(['message' => 'Job owner not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'image' => $user->image,
            'user_type' => $user->user_type,
        ]);
    }

    public function showCompany(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'company'|| $user->user_type !== 'admin') {
            return response()->json(['message' => 'Company not found'], 404);
        }

        return response()->json([
            'id'=>$user->id,
            'name' => $user->name,
            'email' => $user->email,
            'commercial_register'=> $user->commercial_register,
            'user_type' => $user->user_type,
        ]);
    }
      public function showAdmin(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'admin') {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'image' => $user->image,
            'user_type' => $user->user_type,
        ]);

    }
    public function getAllUsers(Request $request)
    {
        // Check if the authenticated user is an admin
        if (Auth::user()->user_type !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $users = User::all();
        return response()->json($users);
    }

    public function getAllCompanies(Request $request)
    {
        // Check if the authenticated user is an admin
        if (Auth::user()->user_type !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $companies = Companies::all();
        return response()->json($companies);
    }
    public function showUserById($id)
    {
        $user = Auth::user();

        try {
            $user = User::findOrFail($id);
            return response()->json($user);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
    }
    public function showCompanyById($id)
    {
        try {
            $company = Companies::findOrFail($id);
            return response()->json($company);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Company not found'], 404);
        }
    }
    public function getFlagByUserId($id)
{
    try {
        $user = User::findOrFail($id);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'User not found'], 404);
    }

    return response()->json(['flag' => $user->flag]);
}





}
