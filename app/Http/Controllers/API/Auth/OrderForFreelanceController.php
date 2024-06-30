<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\CV;
use App\Models\JobOwner;
use App\Models\JObsForFreelancers;
use App\Models\Order;
use App\Models\OrderForFreelance;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OrderForFreelanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if the user is an admin
        if ($user->user_type === 'admin') {
            // Fetch all orders
            $orders = OrderForFreelance::all();
            return response()->json($orders);
        } else {
            // Fetch the user's own orders
            $orders = $user->orders;
            return response()->json($orders);
        }
    }
    public function show($id){
        $order = OrderForFreelance::findOrFail($id);
        return response()->json($order);

    }
    public function destroy($id)
{
    $order = OrderForFreelance::findOrFail($id);
    $order->delete();
    return response()->json(['message' => 'Order deleted']);
}
public function getOrdersByJobOwnerId($UserId)
{
    $orders = OrderForFreelance::where('user_id', $UserId)->get();

    // Initialize an empty array to hold the orders
    $arr = [];

    // Iterate through each order in the collection
    foreach($orders as $order) {
        // Add each order to the array
        $arr[] = $order;
    }

    // Return the array of orders wrapped in a JSON response
    return response()->json(['message' => $arr], 200);
}

public function createOrder(Request $request)
{
    // Validate the input data
    $validatedData = $request->validate([
        'j_obs_for_freelancers_id' => 'required|exists:j_obs_for_freelancers,id',
        'user_id' => 'required|exists:users,id',
        'job_owner_id' => 'required|exists:job_owners,id',
    ]);

    // Get the job details from the JobsForFreelancers model
    $job = JObsForFreelancers::with('requirements')->findOrFail($validatedData['j_obs_for_freelancers_id']);
    $job_owner = JobOwner::findOrFail($validatedData['job_owner_id']);
    $user = User::findOrFail($validatedData['user_id']);

    // Check if the user has already created an order for this job
    $existingOrder = OrderForFreelance::where('j_obs_for_freelancers_id', $validatedData['j_obs_for_freelancers_id'])
                                      ->where('user_id', $validatedData['user_id'])
                                      ->where('job_owner_id', $validatedData['job_owner_id'])
                                      ->first();

    if ($existingOrder) {
        return response()->json(['message' => 'You have already applied for this job.'], 400);
    }

    // Retrieve the user's CV ID
    $userCV = CV::where('user_id', $validatedData['user_id'])->first();
    $cv_id = $userCV ? $userCV->id : null;

    // Check if the user has a CV
    if ($cv_id) {
        // Check if the user has the required skills
        $userSkills =UserSkill::where('user_id', $validatedData['user_id'])->pluck('skill_id')->toArray();
        $requiredSkills = $job->requirements->pluck('skill_id')->toArray();
        $missingSkills = array_diff($requiredSkills, $userSkills);

        if (!empty($missingSkills)) {
            return response()->json([
                'message' => 'You don\'t have enough skills to apply for this job.',
                'missing_skills' => $missingSkills
            ], 400);
        }

        $order = new OrderForFreelance();
        $order->j_obs_for_freelancers_id = $validatedData['j_obs_for_freelancers_id'];
        $order->user_id = $validatedData['user_id'];
        $order->job_owner_id = $validatedData['job_owner_id'];
        $order->cv_id = $cv_id;
        $order->save();

        // Return the order and CV ID in the response
        return response()->json([
            'order' => $order,
        ], 201);
    } else {
        // Return a message if the user doesn't have a CV
        return response()->json([
            'message' => 'You need to upload a CV before applying for this job.'
        ], 400);
    }
}
    public function update(Request $request, $id)
    {
        $order = OrderForFreelance::findOrFail($id);
        $validatedData = $request->validate([
            'order_status' => 'required|in:processing,rejected,accepted',
        ]);
        $order->order_status = $request->order_status;
        $order->save();
        return response()->json($order);
    }
    public function getAllOrdersForUser(Request $request, $userId)
    {
        // Retrieve the orders for the specified user
        $orders = OrderForFreelance::with('job', 'user')
                                  ->whereHas('user', function ($query) use ($userId) {
                                      $query->where('id', $userId);
                                  })
                                  ->get();

        // Return the orders as a JSON response
        return response()->json($orders, 200);
    }

    public function getOrdersByJobOwnerAndJobForFreelanceId($job_owner_id, $j_obs_for_freelancers_id)
{
    $orders = OrderForFreelance::where('job_owner_id', $job_owner_id)
                               ->where('j_obs_for_freelancers_id', $j_obs_for_freelancers_id)
                               ->get();

    $orderData = [];

    foreach ($orders as $order) {
        $orderData[] = [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'j_obs_for_freelancers_id' => $order->j_obs_for_freelancers_id,
            'job_owner_id' => $order->job_owner_id,
            'cv_id' => $order->cv_id,
        ];
    }

    return response()->json(['orders' => $orderData], 200);
}
}
