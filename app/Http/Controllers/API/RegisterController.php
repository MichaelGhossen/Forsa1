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
        ]);

        $company = Companies::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'commercial_register' => $validatedData['commercial_register'],
            'user_type' => $validatedData['user_type'], // Set the user_type
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
}
