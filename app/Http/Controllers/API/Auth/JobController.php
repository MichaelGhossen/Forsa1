<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::select('id', 'title', 'category_id')->get();
        return response()->json(['message'=>$jobs],200);
    }

    public function show($id)
    {
        $job = Job::findOrFail($id);
        return response()->json($job);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Check if the user is a company, or admin
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        $job = Job::findOrFail($id);
        if (!$job) {
            return response()->json(['error' => 'You dont have an job.'], 401);
        }

        if ($user->user_type === 'company'||$user->user_type === 'admin') {
    $validatedData = $request->validate([
    'title' => 'required|string|max:255',
    'min_duration' => 'required|date',
    'max_duration' => 'required|date',
    'min_age' => 'required|integer',
    'max_age' => 'required|integer',
    'min_salary' => 'nullable|numeric',
    'max_salary' => 'nullable|numeric',
    'gender' => 'nullable',
    'languages' => 'required',
    'description' => 'required|string',
    'category_id' => 'required|exists:categories,id',
    'location' => 'nullable|string',
    'company_id'=>'required|exists:companies,id',
    'user_id'=>'nullable',
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
        $job = Job::findOrFail($id);

        // Check if the user is the owner of the job or an admin
        if ($user->user_type === 'company'||$user->user_type === 'admin') {

        $job->delete();
        return response()->json(['message' => 'Job deleted successfully']);
    }
    else{
        return response()->json(['error' => 'You are not authorized to delete this job.'], 403);

    }
}

    public function searchJob(Request $request)
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

        $jobs = Job::where('title', 'like', '%' . $request->title . '%')->get();

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
//new
public function create(Request $request)
{
    $user = Auth::user();

    // Check if the user is a job owner, company, or admin
    // if (!$user) {
    //     return response()->json(['error' => 'Unauthenticated.'], 401);
    // }

    if ($user->user_type === 'company' || $user->user_type === 'admin') {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'min_duration' => 'required|date',
            'max_duration' => 'required|date',
            'min_age' => 'required|integer',
            'max_age' => 'required|integer',
            'min_salary' => 'nullable|numeric',
            'max_salary' => 'nullable|numeric',
            'gender' => 'nullable',
            'languages' => 'required',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'location' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
            'user_id' => 'nullable',
        ]);

        if ($user->user_type !== 'admin') {
            // Get the company account
            $companyAccount = Account::where('company_id', $validatedData['company_id'])->first();

            if ($companyAccount) {
                // Calculate the amount to deduct from the company account
                $amountToDeduct = 15; // Fixed amount of 15
                $remainingAmount = $companyAccount->amount - $amountToDeduct;

                if ($companyAccount->amount >= $amountToDeduct) {
                    // Update the company account
                    $companyAccount->amount -= $amountToDeduct;
                    $companyAccount->save();

                    // Get the admin account
                    $adminAccount = Account::where('user_id', 1)->first(); // Assuming the admin user has ID 1

                    // Add the deducted amount to the admin account
                    $adminAccount->amount += $amountToDeduct;
                    $adminAccount->save();

                    $job = Job::create($validatedData);
                    return response()->json([
                        'job' => $job,
                        'remaining_company_account_balance' => $remainingAmount
                    ], 201);
                }
            }
        }

        // Create the job regardless of the company account balance
        $job = Job::create($validatedData);
        return response()->json([
            'job' => $job,
        ], 201);
    } else {
        return response()->json(['error' => 'User not authorized.'], 403);
    }
}

public function jobsByCompany($company_id)
{
    $jobs = Job::where('company_id', $company_id)
        ->select('id', 'title', 'category_id')
        ->get();

    return response()->json(['jobs' => $jobs]);
}
public function getJobsByUserId($userId)
{
    $jobs = Job::where('user_id', $userId)
        ->select('id', 'title', 'category_id')
        ->get();

        return response()->json(['jobs' => $jobs]);
    }
    public function getJobsByFilters($company_id, $category_id)
    {
        $query = Job::query();

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        $jobs = $query->select('id', 'title', 'category_id')
            ->get();

            return response()->json(['jobs' => $jobs]);
        }
        public function getJobsByAdminCategory($user_id, $category_id)
        {
            $query = Job::query();
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
public function searchJobByCompanyId(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required',
        'company_id' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Search fields are required',
            'errors' => $validator->errors(),
        ], 422);
    }

    $query = Job::where('title', 'like', '%' . $request->title . '%');

    if ($request->has('company_id')) {
        $query->where('company_id', $request->company_id);
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
public function searchJobByAdminId(Request $request)
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

    $query = Job::where('title', 'like', '%' . $request->title . '%');

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
public function getJobName($jobId)
{
    // Retrieve the job record
    $job = Job::findOrFail($jobId);

    // Return the job name as a JSON response
    return response()->json([
        'title' => $job->title
    ]);
}
}
