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
        $jobs = Job::all();
        $arr[]=null;
        $i=0;
        foreach($jobs as $jobs)
     {  $arr[$i++]=$jobs;
    }
        return response()->json(['message'=>$arr],200);
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
    'company_id'=>'required',

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
        if ($user->user_type !== 'admin') {
            return response()->json(['error' => 'You are not authorized to delete this job.'], 403);
        }

        $job->delete();
        return response()->json(['message' => 'Job deleted successfully']);
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
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }

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
            'company_id' => 'nullable',
        ]);
        if ($user->user_type !== 'admin') {

        // Get the company account
        $companyAccount = Account::where('company_id', $validatedData['company_id'])->first();

        // Calculate the amount to deduct from the company account
        $amountToDeduct = $companyAccount->amount * 0.1; // 10% deduction
        $remainingAmount = $companyAccount->amount - $amountToDeduct;

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
        ], 201);}
        else {
        $job = Job::create($validatedData);
        return response()->json([
            'job' => $job,
        ], 201);}
    } else {
        return "user not auth";
    }
}

}
