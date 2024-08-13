<?php

namespace App\Http\Controllers\API\Auth;

use App\Events\NewNotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\Companies;
use App\Models\CV;
use App\Models\Job;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if the user is an admin
        if ($user->user_type === 'admin') {
            // Fetch all orders
            $orders = Order::all();
            return response()->json($orders);
        } else {
            return response()->json(['error' => 'You are not authorized to delete this job.'], 403);
        }
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'company_id' => 'required|exists:companies,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $job = Job::findOrFail($request->job_id);
        $company = Companies::findOrFail($request->company_id);
        $user = User::findOrFail($request->user_id);

        // Check if the user has already applied for the job
        $existingOrder = Order::where('user_id', $request->user_id)
            ->where('job_id', $job->id)
            ->where('company_id', $company->id)
            ->first();
        if ($existingOrder) {
            return response()->json(['message' => 'You have already applied for this job.'], 400);
        }

        // Retrieve the user's CV ID
        $userCV = CV::where('user_id', $request->user_id)->first();
        $cv_id = $userCV ? $userCV->id : null;

        // Check if the user has a CV
        if ($cv_id) {
            $order = new Order();
            $order->user_id = $request->user_id;
            $order->job_id = $job->id;
            $order->company_id = $company->id;
            $order->cv_id = $cv_id;
            $order->save();

            // Return the order and CV ID in the response
            return response()->json([
                'order' => $order,
                //  'cv_id' => $cv_id
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
        if($user['user_type']!='company'){
            return response()->json(null,403);
        }
        $order = Order::findOrFail($id);
        if($user['id']!=$order->company_id){
            return response()->json(null,403);
        }
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

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['message' => 'Order deleted']);
    }
    public function getOrdersByCompanyId($companyId)
    {

        $orders = Order::where('company_id', $companyId)->get();
        $arr[] = null;
        $i = 0;
        foreach ($orders as $orders) {
            $arr[$i++] = $orders;
        }
        return response()->json(['message' => $arr], 200);
    }
    public function getAllOrders(Request $request, $id)
    {
        // Retrieve the user using the $id
        $user = User::findOrFail($id);

        // Load the favorite jobs for the user, including the related job data
        $orders = $user->orders()->get();
        // Return the favorite jobs as a JSON response
        return response()->json($orders);
    }
    public function getOrdersByCompanyAndJobId($companyId, $jobId)
    {
        $orders = Order::where('company_id', $companyId)
            ->where('job_id', $jobId)
            ->get();

        $orderData = [];

        foreach ($orders as $order) {
            $orderData[] = [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'job_id' => $order->job_id,
                'company_id' => $order->company_id,
                'cv_id' => $order->cv_id,
            ];
        }

        return response()->json(['orders' => $orderData], 200);
    }
    public function getOrdersByStatusAndUser($status, $userId)
    {
        $orders = Order::whereIn('order_status', ['accepted', 'rejected', 'processing'])
                    ->where('user_id', $userId)
                    ->when($status, function ($query) use ($status) {
                        $query->where('order_status', $status);
                    })
                    ->get();

        return response()->json(['orders' => $orders], 200);
    }
}
