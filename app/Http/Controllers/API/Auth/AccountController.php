<?php

namespace App\Http\Controllers\API\Auth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->user_type === 'admin') {
        $accounts = Account::all();
        return response()->json($accounts, Response::HTTP_OK);
    }
    else{
        return response()->json(['error' => 'You are not authorized.'], 403);
    }
    }
    /**
     * Store a newly created account in storage.
     */
    public function store(Request $request)
{   $user = Auth::user();
    if ($user->user_type === 'admin') {
       $request->validate([
    'user_id' => 'nullable|integer',
    'company_id' => 'nullable|integer',
    'amount' => 'required|numeric',
]);

$account = Account::create([
    'user_id' => $request->input('user_id'),
    'company_id' => $request->input('company_id'),
    'amount' => $request->input('amount'),
]);

return response()->json([
    'user_id' => $account->user_id,
    'company_id' => $account->company_id,
    'amount' => $account->amount,
    'updated_at' => $account->updated_at,
    'created_at' => $account->created_at,
    'id' => $account->id,
], Response::HTTP_CREATED);

    return response()->json($account, Response::HTTP_CREATED);
}
else{
    return response()->json(['error' => 'You are not authorized.'], 403);
}
}

    /**
     * Display the specified account.
     */
    public function show($id)
    {
        $account = Account::findOrFail($id);
        return response()->json($account, Response::HTTP_OK);
    }

    /**
     * Update the specified account in storage.
     */
    public function update(Request $request, $id)
    { $user = Auth::user();
        if ($user->user_type === 'admin') {
        $request->validate([
            'user_id' => 'nullable|integer',
            'company_id' => 'nullable|integer',
            'amount' => 'required|numeric',
        ]);

        $account = Account::findOrFail($id);

        // Update the account fields
        $account->user_id = $request->input('user_id');
        $account->company_id = $request->input('company_id');
        $account->amount = $request->input('amount');
        $account->save();

        return response()->json($account, Response::HTTP_OK);
    }else{
        return response()->json(['error' => 'You are not authorized.'], 403);
    }
}

    /**
     * Remove the specified account from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->user_type === 'admin') {
        $account = Account::findOrFail($id);
        $account->delete();

        return response()->json(['Account deleted successfully']);
        }else{
        return response()->json(['error' => 'You are not authorized.'], 403);
    }
}
}
