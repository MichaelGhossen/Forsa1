<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Companies;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class DeleteController extends Controller
{
    /**
     * Delete a jobseeker user
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteJobseeker(Request $request)
    {
        $user = User::where('id', $request->input('id'))->where('user_type', 'job_seeker')->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Job_seeker user deleted successfully']);
    }

    /**
     * Delete a jobowner user
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteJobowner(Request $request)
    {
        $user = User::where('id', $request->input('id'))->where('user_type', 'job_owner')->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Job_owner user deleted successfully']);
    }
    /**
     * Delete a company user
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCompany(Request $request)
    {
        $user = User::where('id', $request->input('id'))->where('user_type', 'company')->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Company user deleted successfully']);
    }

    public function deleteJobSeekerById($id)
    {
        try {
            $jobSeeker = User::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Job seeker not found'], 404);
        }

        $user = Auth::user();

        // Check if the user is an admin or the job seeker themselves
        if ($user->user_type === 'admin' || $user->user_type === 'job_seeker') {
            $jobSeeker->delete();
        }
        else{
            return response()->json(['error' => 'You are not authorized to delete this job seeker.'], 403);
        }

        return response()->json(['message' => 'Job seeker deleted successfully']);
    }

    public function deleteJobOwnerById($id)
    {
        try {
            $jobOwner = User::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Job owner not found'], 404);
        }

        $user = Auth::user();

        // Check if the user is an admin or the job seeker themselves
        if ($user->user_type === 'admin' || $user->user_type === 'job_owner') {
            $jobOwner->delete();
        }
        else{
            return response()->json(['error' => 'You are not authorized to delete this job owner.'], 403);
        }

        return response()->json(['message' => 'Job owner deleted successfully']);
    }
    public function deleteCompanyById($id)
{
    try {
        $company = Companies::findOrFail($id);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Company not found'], 404);
    }

    $user = Auth::user();

    // Check if the user is an admin or the company owner
    if ($user->user_type === 'admin' || $user->user_type === 'company') {
        $company->delete();
    }
    else{
    return response()->json(['error' => 'You are not authorized to delete this company.'], 403);
    }
    return response()->json(['message' => 'Company deleted successfully']);
}
}
