<?php

namespace App\Http\Controllers\API\Auth;

use App\Events\NewNotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\CV;
use App\Models\JobOwner;
use App\Models\JObsForFreelancers;
use App\Models\Order;
use App\Models\OrderForFreelance;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            return response()->json(['error' => 'You are not authorized to delete this job.'], 403);
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
public function getOrdersByJobOwnerId($jobOwnerId)
{
    try {
      $jobOwner = JobOwner::findOrFail($jobOwnerId);
    $orders = OrderForFreelance::where('job_owner_id', $jobOwnerId)->get();

    // Initialize an empty array to hold the orders
    $arr = [];

    // Iterate through each order in the collection
    foreach($orders as $order) {
        // Add each order to the array
        $arr[] = $order;
    }

    // Return the array of orders wrapped in a JSON response
    return response()->json(['message' => $arr], 200);
} catch (ModelNotFoundException $e) {
        // Handle the case when the job owner is not found
        return response()->json(['error' => 'Job owner not found'], 404);
    }
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
        $user=Auth::user();
        if($user['user_type']!='job_owner'){
            return response()->json(null,403);
        }
        $order = OrderForFreelance::findOrFail($id);
        $validatedData = $request->validate([
            'order_status' => 'required|in:processing,rejected,accepted',
        ]);
        $order->order_status = $request->order_status;
        $order->save();
        $userId =$order->user_id;
        $notification = ['message' => 'the order status is '.$order->order_status];
        event(new NewNotificationEvent($userId, $notification));
        return response()->json($order);
    }
    public function getAllOrdersForUser(Request $request, $userId)
    {
        // Retrieve the orders for the specified user
        $orders = OrderForFreelance::with('job', 'user')
                                  ->whereHas('user', function ($query) use ($userId) {
                                      $query->where('id', $userId);
                                  })
                                  ->get([
                                      'id',
                                      'j_obs_for_freelancers_id',
                                      'user_id',
                                      'cv_id',
                                      'job_owner_id',
                                      'order_status',
                                      'created_at',
                                      'updated_at',
                                  ]);

        // Map the orders to the desired format
        $orderData = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'j_obs_for_freelancers_id' => $order->j_obs_for_freelancers_id,
                'user_id' => $order->user_id,
                'cv_id' => $order->cv_id,
                'job_owner_id' => $order->job_owner_id,
                'order_status' => $order->order_status,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,

            ];
        });

        // Return the orders as a JSON response
        return response()->json($orderData, 200);
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
public function getOrdersByStatus($status)
{
    $orders = OrderForFreelance::whereIn('order_status', ['accepted', 'rejected', 'processing'])
                   ->when($status, function ($query) use ($status) {
                       $query->where('order_status', $status);
                   })
                   ->get();

    return response()->json(['orders For Freelance' => $orders], 200);
}
}
