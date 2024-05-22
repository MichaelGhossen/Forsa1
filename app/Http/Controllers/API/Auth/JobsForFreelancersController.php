<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\JobsForFreelancers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class JobsForFreelancersController extends Controller
{
    public function index()
    {
        $jobs = JobsForFreelancers::all();
        return response()->json($jobs);
    }

    public function show($id)
    {
        $job = JobsForFreelancers::findOrFail($id);
        return response()->json($job);
    }
    public function create(Request $request)
    {
        $user = Auth::user();

        // Check if the user is a job owner
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        if ($user->user_type === 'job_owner') {
        $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'min_duration' => 'required|date',
        'max_duration' => 'required|date',
        'requirements' => 'required|string',
        // 'min_age' => 'required|integer',
        // 'max_age' => 'required|integer',
        'min_salary' => 'nullable|numeric',
        'max_salary' => 'nullable|numeric',
        // 'gender' => 'nullable',
        'languages' => 'required',
        'description' => 'required|string',
        // 'location' => 'nullable|string',

        ]);

        $job = JobsForFreelancers::create($validatedData);

        return response()->json($job, 201);
        }
        else{
            return "user not auth";
        }
    }
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Check if the user is a job owner
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        $job = JobsForFreelancers::findOrFail($id);
        if (!$job) {
            return response()->json(['error' => 'You dont have an job.'], 401);
        }

        if ($user->user_type === 'job_owner') {
    $validatedData = $request->validate([
    'title' => 'required|string|max:255',
    'min_duration' => 'required|date',
    'max_duration' => 'required|date',
    'requirements' => 'required|string',
    // 'min_age' => 'required|integer',
    // 'max_age' => 'required|integer',
    'min_salary' => 'nullable|numeric',
    'max_salary' => 'nullable|numeric',
    // 'gender' => 'nullable',
    'languages' => 'required',
    'description' => 'required|string',
    // 'location' => 'nullable|string',

            ]);

            $job ->update($validatedData);

            return response()->json($job, 201);
    }
            else{
                return "user not auth";
            }

        }
    public function destroy($id)
    {
        $user = Auth::user();
        $job = JobsForFreelancers::findOrFail($id);

        // Check if the user is the job_owner
        if ($user->user_type !== 'job_owner') {
            return response()->json(['error' => 'You are not authorized to delete this job.'], 403);
        }

        $job->delete();
        return response()->json(['message' => 'Job deleted successfully']);
    }

}
