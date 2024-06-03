<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JobsForFreelancers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class JobsForFreelancersController extends Controller
{
    public function index()
    {
        $jobs = JobsForFreelancers::all();
        $arr[]=null;
        $i=0;
        foreach($jobs as $jobs)
     {  $arr[$i++]=$jobs;
    }
        return response()->json(['message'=>$arr],200);
    }

    public function show($id)
    {
        $job = JobsForFreelancers::findOrFail($id);
        return response()->json($job);
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
    'min_salary' => 'nullable|numeric',
    'max_salary' => 'nullable|numeric',
    'languages' => 'required',
    'description' => 'required|string',
    'category_id' => 'required|exists:categories,id',
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


    public function searchJobFreelancer(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Search field is required',
            'errors' => $validator->errors(),
        ], 422);
    }

    $jobs = JobsForFreelancers::where('title', 'like', '%' . $request->title . '%')->get();

    if ($jobs->isNotEmpty()) {
        return response()->json([
            'data' => $jobs,
            'message' => 'Found jobs',
        ], 200);
    } else {
        return response()->json([
            'data' => [],
            'message' => 'No freelancer jobs matched your search',
        ], 404);
    }
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
            'min_salary' => 'nullable|numeric',
            'max_salary' => 'nullable|numeric',
            'languages' => 'required',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',

        ]);

        // Get the job owner's account
        $jobOwnerAccount = Account::where('user_id', $user->id)->first();

        // Calculate the amount to deduct from the job owner's account
        $amountToDeduct = 50; // For example, deduct $50 for creating a job

        // Check if the job owner has enough balance
        if ($jobOwnerAccount->amount >= $amountToDeduct) {
            // Update the job owner's account
            $jobOwnerAccount->amount -= $amountToDeduct;
            $jobOwnerAccount->save();

            $job = JobsForFreelancers::create($validatedData);

            return response()->json([
                'job' => $job,
                'remaining_job_owner_account_balance' => $jobOwnerAccount->amount
            ], 201);
        } else {
            return response()->json(['error' => 'Insufficient balance.'], 400);
        }
    } else {
        return "user not auth";
    }
}
}
