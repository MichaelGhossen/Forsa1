<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Companies;
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
            // Fetch the user's own orders
            $orders = $user->orders;
            return response()->json($orders);
        }
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'company_id' => 'required|exists:companies,id',
            'cv_id' => 'required|exists:c_v_s,id',
        ]);

        $job = Job::findOrFail($request->job_id);
        $company = Companies::findOrFail($request->company_id);
        // Check if the user has already applied for the job
        $existingOrder = Order::where('user_id', Auth::id())
                            ->where('job_id', $job->id)
                            ->where('company_id', $company->id)
                            ->first();
        if ($existingOrder) {
            return response()->json(['message' => 'You have already applied for this job.'], 400);
        }
        $order = new Order();
        $order->user_id = Auth::id();
        $order->job_id = $job->id;
        $order->company_id = $company->id;
        $order->save();
        return response()->json($order, 201);
    }


    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validatedData = $request->validate([
            'order_status' => 'required|in:processing,rejected,accepted',
        ]);
        $order->order_status = $request->order_status;
        $order->save();
        return response()->json($order);
    }

    public function show($id){
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
        $arr[]=null;
        $i=0;
        foreach($orders as $orders)
     {  $arr[$i++]=$orders;
}
        return response()->json(['message'=>$arr],200);
    }
    public function getAllOrders(Request $request, $id)
    {
        // Retrieve the user using the $id
        $user = User::findOrFail($id);

        // Load the favorite jobs for the user, including the related job data
        $orders = $user->orders()
                             ->get();
        // Return the favorite jobs as a JSON response
        return response()->json($orders);
    }
}

