<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JobOwner;
use App\Models\Skill;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function register(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Response(['message' => $validator->errors()], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'is_jop_seeker'=>1,

        ]);

        $success = $user->createToken('MyApp')->plainTextToken;

        return Response(['token' => $success], 201);
    }
    public function loginUser(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){

            return Response(['message' => $validator->errors()],401);
        }

        if(Auth::attempt($request->all())){

            $user = Auth::user();

            $success =  $user->createToken('MyApp')->plainTextToken;

            return Response(['token' => $success],200);
        }

        return Response(['message' => 'email or password wrong'],401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function userDetails(): Response
    {
        if (Auth::check()) {

            $user = Auth::user();

            return Response(['data' => $user],200);
        }

        return Response(['data' => 'Unauthorized'],401);
    }

    /**
     * Display the specified resource.
     */
    public function logout(): Response
    {
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return Response(['data' => 'User Logout successfully.'],200);
    }
    public function chooseSkills(Request $request)
{
    $user = auth()->user();

    $validator = validator($request->all(), [
        'skills' => ['required', 'array'],
        'skills.*.skill_id' => ['required', 'exists:skills,id'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 422);
    }

    $skillIds = $request->input('skills.*.skill_id');

    // Sync the user's skills
    $user->skills()->sync($skillIds);

    // Refresh the user model to get the updated skills
    $user->refresh();

    $userSkills = $user->skills->pluck('id')->toArray();

    return response()->json([
        'message' => 'Skills added successfully',
        'skills' => $userSkills
    ], 200);
}

public function getAllJobOwners(): JsonResponse
{
    $user = Auth::user();
    if ($user->user_type === 'admin') {
    $jobOwners = User::where('user_type', 'job_owner')->get();
    return response()->json($jobOwners);
}
else{
    return response()->json(['error' => 'You are not authorized.'], 403);
}
}
public function getAllJobSeekers(): JsonResponse
{
    $user = Auth::user();
    if ($user->user_type === 'admin') {
    $jobseekers = User::where('user_type', 'job_seeker')->get();
    return response()->json($jobseekers);
}
else{
    return response()->json(['error' => 'You are not authorized.'], 403);
}
}

public function getJobOwnerIdByUserId($user_id)
{
    $jobOwner = JobOwner::where('user_id', $user_id)->first();
    return response()->json($jobOwner);
}
}
