<?php
namespace App\Http\Controllers\API\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

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
            return response(['token' => $success], 200);
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
            return response(['token' => $success], 200);
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
            return response(['token' => $success], 200);
        }

        return response(['message' => 'Invalid credentials'], 401);
    }
    public function loginFreelancer(Request $request): Response
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
            if ($user->user_type !== 'freelancer') {
                return response(['message' => 'Unauthorized'], 401);
            }

            $success = $user->createToken('MyApp')->plainTextToken;
            return response(['token' => $success], 200);
        }

        return response(['message' => 'Invalid credentials'], 401);
    }
    public function loginCompany(Request $request): Response
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
            if ($user->user_type !== 'company') {
                return response(['message' => 'Unauthorized'], 401);
            }

            $success = $user->createToken('MyApp')->plainTextToken;
            return response(['token' => $success], 200);
        }

        return response(['message' => 'Invalid credentials'], 401);
    }
}
