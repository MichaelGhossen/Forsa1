<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\JObsForFreelancers;
use App\Models\OrderForFreelance;
use App\Models\User;
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
        ]);

        // Get the job details from the JobsForFreelancers model
        $job = JObsForFreelancers::with('requirements')->findOrFail($validatedData['j_obs_for_freelancers_id']);

        // Check if the user has already created an order for this job
        $existingOrder = OrderForFreelance::where('j_obs_for_freelancers_id', $validatedData['j_obs_for_freelancers_id'])
                                          ->where('user_id', $validatedData['user_id'])
                                          ->first();

        if ($existingOrder) {
            return response()->json(['message' => 'You have already created an order for this job.', 'required_skills' => $job->requirements->toArray()], 400);
        }

        // Create the order
        $order = new OrderForFreelance([
            'j_obs_for_freelancers_id' => $validatedData['j_obs_for_freelancers_id'],
            'user_id' => $validatedData['user_id'],
        ]);
        $order->save();

        $requiredSkills = $job->requirements->pluck('name')->toArray();
        return response()->json(['message' => 'Order created successfully', 'order' => $order, 'required_skills' => $job->requirements->toArray()], 201);
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
}
