<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JobsForFreelancers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class JobsForFreelancersController extends Controller
{
    public function index(Request $request)
    {
        $jobs = JobsForFreelancers::select('id', 'title', 'category_id');

        if ($request->has('skills')) {
            $jobs = $jobs->whereHas('skills', function ($query) use ($request) {
                $query->whereIn('id', $request->get('skills'));
            });
        }

        $jobs = $jobs->get();
        return response()->json(['message' => $jobs], 200);
    }

    public function show($id)
    {
        $job = JobsForFreelancers::with('skills')->findOrFail($id);
        return response()->json($job);
    }
    public function update(Request $request, $id)
{
    $user = Auth::user();

    // Check if the user is a job owner
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }

    $job = JobsForFreelancers::with('skills')->findOrFail($id);
    if ($user->user_type === 'job_owner') {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'min_duration' => 'required|date',
            'max_duration' => 'required|date',
            'skills' => ['required', 'array'],
            'skills.*.skill_id' => ['required', 'exists:skills,id'],
            'salary' => 'nullable|numeric',
            'languages' => 'required',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',

        ]);

        // Update the job details
        $job->title = $validatedData['title'];
        $job->min_duration = $validatedData['min_duration'];
        $job->max_duration = $validatedData['max_duration'];
        $job->salary = $validatedData['salary'];
        $job->languages = $validatedData['languages'];
        $job->description = $validatedData['description'];
        $job->category_id = $validatedData['category_id'];
        $job->user_id = $validatedData['user_id'];
        $job->save();
        // Attach the selected skills to the job
        $job->skills()->sync($validatedData['skills']);

        // Load the updated job and its skills
        $job->load('skills');

        return response()->json($job, 200);
    } else {
        return "User not authorized";
    }
}
    public function destroy($id)
    {
        $user = Auth::user();
        try {
            $job = JobsForFreelancers::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        // Check if the user is the job_owner ||
        if ($user->user_type === 'job_owner'||$user->user_type === 'admin') {
            $job->delete();
        }
        else{
            return response()->json(['error' => 'You are not authorized to delete this job.'], 403);
        }

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
            'skills' => ['required', 'array'],
            'skills.*.skill_id' => ['required', 'exists:skills,id'],
            'salary' => 'nullable|numeric',
            'languages' => 'required',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',

        ]);

        // Get the job owner's account
        $jobOwnerAccount = Account::where('user_id', $user->id)->first();

        // Calculate the amount to deduct from the job owner's account
        $amountToDeduct = 15; // For example, deduct $50 for creating a job

        // Check if the job ow ner has enough balance
        if ($jobOwnerAccount->amount >= $amountToDeduct) {
            // Update the job owner's account
            $jobOwnerAccount->amount -= $amountToDeduct;
            $jobOwnerAccount->save();

            $job = JobsForFreelancers::create([
                'title' => $validatedData['title'],
                'min_duration' => $validatedData['min_duration'],
                'max_duration' => $validatedData['max_duration'],
                'salary' => $validatedData['salary'],
                'languages' => $validatedData['languages'],
                'description' => $validatedData['description'],
                'category_id' => $validatedData['category_id'],
                'user_id'=>$validatedData['user_id'],
            ]);

            // Attach the selected skills to the job
            $job->skills()->sync($validatedData['skills']);

            // Retrieve the job details
            $jobDetails = JobsForFreelancers::with('skills')->find($job->id);

            return response()->json([
                'job' => $jobDetails,
                'remaining_job_owner_account_balance' => $jobOwnerAccount->amount
            ], 201);
        } else {
            return response()->json(['error' => 'Insufficient balance.'], 400);
        }
    } else {
        return "user not auth";
    }
}
public function getJobsByJobOwnerId($userId)
{
    $jobs = JobsForFreelancers::where('user_id', $userId)
        ->select('id', 'title', 'category_id')
        ->get();
        return response()->json(['jobsForFreelance' => $jobs]);
}
public function getJobsFreelanceByJobOwnerAndCategroyId($user_id, $category_id)
{
    $query = JobsForFreelancers::query();

    if ($user_id) {
        $query->where('user_id', $user_id);
    }

    if ($category_id) {
        $query->where('category_id', $category_id);
    }

    $jobs = $query->select('id', 'title', 'category_id')
        ->get();
        return response()->json(['jobs' => $jobs]);
}
public function searchJobByOwnerId(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required',
        'user_id' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Search fields are required',
            'errors' => $validator->errors(),
        ], 422);
    }

    $query = JobsForFreelancers::where('title', 'like', '%' . $request->title . '%');

    if ($request->has('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    $jobs = $query->get();

    if ($jobs->isNotEmpty()) {
        return response()->json([
            'data' => $jobs,
            'message' => 'Found jobs',
        ], 200);
    } else {
        return response()->json([
            'data' => [],
            'message' => 'No jobs matched your search',
        ], 404);
    }
}
}
