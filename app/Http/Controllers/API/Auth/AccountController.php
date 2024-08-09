<?php

namespace App\Http\Controllers\API\Auth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Companies;
use App\Models\User;
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

    public function store(Request $request)
    {
    $user = Auth::user();

    if ($user->user_type === 'admin') {
        $request->validate([
            'user_id' => 'nullable|integer|unique:accounts,user_id',
            'company_id' => 'nullable|integer|unique:accounts,company_id',
            'amount' => 'required|numeric',
        ]);

        // Ensure that either user_id or company_id is provided
        if ($request->input('user_id') === null && $request->input('company_id') === null) {
            return response()->json(['error' => 'Either user_id or company_id must be provided.'], 400);
        }

        // Check if the user or company already has an account
        if ($request->input('user_id')) {
            $existingAccount = Account::where('user_id', $request->input('user_id'))->first();
            if ($existingAccount) {
                return response()->json(['error' => 'The user already has an account.'], 400);
            }
        } elseif ($request->input('company_id')) {
            $existingAccount = Account::where('company_id', $request->input('company_id'))->first();
            if ($existingAccount) {
                return response()->json(['error' => 'The company already has an account.'], 400);
            }
        }

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
    } else {
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
public function getAccountAmountForUser($userId)
{
    $user = User::findOrFail($userId);
    $account = $user->account;
    if ($account) {
        return response()->json([
            'account_amount' => $account->amount
        ]);
    } else {
        // Return an error message if the user doesn't have an account
        return response()->json([
            'error' => 'User does not have an account'
        ], 404);
    }
}

// public function getAccountAmountForCompany($companyId)
// {
//     $company = Companies::findOrFail($companyId);
//     $account = $company->account;
//     if ($account) {
//         return response()->json([
//             'account_amount' => $account->amount
//         ]);
//     } else {
//         return response()->json([
//             'error' => 'Company does not have an account'
//         ], 404);
//     }
// }
public function getAccountAmountForCompany($companyId)
{
    $companyAccount = Account::where('company_id', $companyId)->first();

    if ($companyAccount) {
        return response()->json([
            'account_amount' => $companyAccount->amount
        ]);
    } else {
        return response()->json([
            'error' => 'Company does not have an account'
        ], 404);
    }
}
}
