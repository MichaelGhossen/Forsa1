<?php
namespace App\Http\Controllers\API\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Companies;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    public function loginAdmin(Request $request): Response
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response(['message' => $validator->errors()], 401);
    }

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        if ($user->user_type !== 'admin') {
            return response(['message' => 'Unauthorized'], 401);
        }

        $success = $user->createToken('MyApp')->plainTextToken;
        return response([
            'token' => $success,
            'flag' =>$user->flag ,
            'id' => $user->id
        ], 200);
    }

    return response(['message' => 'Invalid credentials'], 401);
}
    public function loginJobSeeker(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 401);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->user_type !== 'job_seeker') {
                return response(['message' => 'Unauthorized'], 401);
            }

            $success = $user->createToken('MyApp')->plainTextToken;
            return response([
                'token' => $success,
                'flag' =>$user->flag ,
                'id' => $user->id
            ], 200);
        }

        return response(['message' => 'Invalid credentials'], 401);
    }
    public function loginJobOwner(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 401);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->user_type !== 'job_owner') {
                return response(['message' => 'Unauthorized'], 401);
            }

            $success = $user->createToken('MyApp')->plainTextToken;
            return response([
                'token' => $success,
                'flag' =>$user->flag ,
                'id' => $user->id
            ], 200);
        }

        return response(['message' => 'Invalid credentials'], 401);
    }
    public function loginCompany(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $company = Companies::where('email', $validatedData['email'])->first();

        if ($company && Hash::check($validatedData['password'], $company->password)) {
            // Generate a token for the company user
            $token = $company->createToken('company_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'flag' => $company->flag,
                'id' => $company->id
            ], 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }
}
