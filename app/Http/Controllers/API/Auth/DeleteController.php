<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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
     * Delete a freelancer user
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    // public function deleteFreelancer(Request $request)
    // {
    //     $user = User::where('id', $request->input('id'))->where('user_type', 'freelancer')->first();

    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     $user->delete();

    //     return response()->json(['message' => 'Freelancer user deleted successfully']);
    // }

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
}
