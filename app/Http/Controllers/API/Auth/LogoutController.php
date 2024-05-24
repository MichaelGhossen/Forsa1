<?php

namespace App\Http\Controllers\API\Auth;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logoutAdmin(): Response
    {
        $user = Auth::user();

        $user->tokens()->delete();

        return response(['data' => 'User logged out successfully.'], 200);
    }

    public function logoutJobSeeker(): Response
    {
        $user = Auth::user();

        $user->tokens()->delete();

        return response(['data' => 'User logged out successfully.'], 200);
    }

    public function logoutJobOwner(): Response
    {
        $user = Auth::user();

        $user->tokens()->delete();

        return response(['data' => 'User logged out successfully.'], 200);
    }

    // public function logoutFreelancer(): Response
    // {
    //     $user = Auth::user();

    //     $user->tokens()->delete();

    //     return response(['data' => 'User logged out successfully.'], 200);
    // }

    public function logoutCompany(): Response
    {
        $user = Auth::user();

        $user->tokens()->delete();

        return response(['data' => 'User logged out successfully.'], 200);
    }
}
