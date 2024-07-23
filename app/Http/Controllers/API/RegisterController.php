<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Companies;
use App\Models\Freelancer;
use App\Models\JobOwner;
use App\Models\JobSeeker;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    public function registerJobSeeker(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'image' => 'nullable|mimes:png,jpg,jpeg,gif',
            'answer'=> 'required|string|max:255',
        ]);

        // Check if the user already has an account
        $existingUser = User::where('email', $validatedData['email'])->first();
        if ($existingUser) {
            return response()->json(['error' => 'The user already has an account.'], 400);
        }
        $imagePath = null;
        $type = null;
        if ($request['image'] != null) {
            $file = $request->file('image');
            $imagePath = 'images/' . time() . $file->getClientOriginalName();
            $type = $file->guessClientExtension();
            Storage::disk('public')->put($imagePath, File::get($file));
        }

        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'image' => $imagePath, //$request->hasFile('image') ? $request->file('image')->store('profile_images', 'public') : null,
            'fileType' => $type,
            'user_type' => 'job_seeker',
            'answer' => $validatedData['answer'],
        ]);
        $jobSeeker = JobSeeker::create([
            'user_id' => $user->id,
        ]);


        return response()->json(['message' => 'Job seeker registered successfully', 'user' => $user]);
    }
    public function registerJobOwner(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'image' => 'nullable|mimes:png,jpg,jpeg,gif',
            'answer'=> 'required|string|max:255',

        ]);
        $imagePath = null;
        $type = null;
        if ($request['image'] != null) {
            $file = $request->file('image');
            $imagePath = 'images/' . time() . $file->getClientOriginalName();
            $type = $file->guessClientExtension();
            Storage::disk('public')->put($imagePath, File::get($file));
        }
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'image' => $imagePath, //$request->hasFile('image') ? $request->file('image')->store('profile_images', 'public') : null,
            'fileType' => $type,
            'user_type' => 'job_owner',
            'answer' => $validatedData['answer'],

        ]);

        $jobOwner = JobOwner::create([
            'user_id' => $user->id,
        ]);

        // Add any additional logic or response as needed, such as sending a welcome email or triggering a notification

        return response()->json(['message' => 'Job owner registered successfully']);
    }


    public function registerCompany(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies|max:255',
            'password' => 'required|string|min:8',
            'commercial_register' => 'required|string|max:255',
            'user_type' => 'required|string|in:company,admin', // Validate the user_type
            'answer'=> 'required|string|max:255',
        ]);

        $company = Companies::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'commercial_register' => $validatedData['commercial_register'],
            'user_type' => $validatedData['user_type'], // Set the user_type
            'answer' => $validatedData['answer'],
        ]);

        return response()->json(['message' => 'Company registered successfully'], 201);
    }


    public function getFile($id)
    {
        try {
            $file = User::where('id', $id)->first();
            if ($file != null) {
                $responseFile = Storage::disk('public')->get($file['image']);
                return (new Response($responseFile, 200))
                    ->header('Content-Type', $file['fileType']);
            }
        } catch (Exception $e) {
            return response()->json([
                "state" => false,
                "data" => $e->getMessage()
            ]);
        }
    }
    public function verifyUserAnswer(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'answer' => 'required|string',
    ]);

    $user = User::where('email', $request->input('email'))->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found.'
        ], 404);
    }

    if ($user->answer === $request->input('answer')) {
        return response()->json([
            'message' => 'Answer verified successfully.'
        ], 200);
    } else {
        return response()->json([
            'message' => 'Invalid answer.'
        ], 400);
    }
}
public function verifyCompanyAnswer(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'answer' => 'required|string',
    ]);

    $user = Companies::where('email', $request->input('email'))->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found.'
        ], 404);
    }

    if ($user->answer === $request->input('answer')) {
        return response()->json([
            'message' => 'Answer verified successfully.'
        ], 200);
    } else {
        return response()->json([
            'message' => 'Invalid answer.'
        ], 400);
    }
}
public function resetPasswordUser(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:8',
    ]);
    $user = User::where('email', $request->input('email'))->first();
    if (!$user) {
        return response()->json([
            'message' => 'User not found.'
        ], 404);
    }

    // Update the password
    $user->password = Hash::make($request->input('password'));
    $user->save();

    return response()->json([
        'message' => 'Password reset successfully.'
    ], 200);
}
public function resetPasswordCompany(Request $request)
{
    // Validate the input
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:8',
    ]);

    // Get the user based on the provided email
    $user = Companies::where('email', $request->input('email'))->first();

    // Check if the user exists
    if (!$user) {
        return response()->json([
            'message' => 'company not found.'
        ], 404);
    }

    // Update the password
    $user->password = Hash::make($request->input('password'));
    $user->save();

    return response()->json([
        'message' => 'Password reset successfully.'
    ], 200);
}
}
