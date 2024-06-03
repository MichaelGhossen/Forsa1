<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowController extends Controller
{
    public function showJobSeeker(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json(['message' => 'Job seeker not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'image' => $user->image,
            // 'cv' => $user->cv,
            'user_type' => $user->user_type,
        ]);
    }

    public function showJobOwner(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'job_owner') {
            return response()->json(['message' => 'Job owner not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'image' => $user->image,
            // 'cv' => $user->cv,
            'user_type' => $user->user_type,
        ]);
    }

    public function showCompany(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'company') {
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
            // 'cv' => $user->cv,
            'user_type' => $user->user_type,
        ]);

    }



}
