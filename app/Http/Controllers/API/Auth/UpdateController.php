<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
class UpdateController extends Controller
{
    public function updateFreelancer(Request $request)
    {
        $user = $request->user();
            if (!$user || $user->user_type !== 'freelancer') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'image' => 'nullable|image|max:2048',
            'cv' => 'nullable|file|max:5120',
        ]);

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');

        if ($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        if ($request->hasFile('image')) {
            $user->image = $request->file('image')->store('public/images');
        }

        if ($request->hasFile('cv')) {
            $user->cv = $request->file('cv')->store('public/cvs');
        }

        $user->save();

        return response()->json($user);
    }

    public function updateJobSeeker(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'image' => 'nullable|image|max:2048',
            'cv' => 'nullable|file|max:5120',
        ]);

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');

        if ($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        if ($request->hasFile('image')) {
            $user->image = $request->file('image')->store('public/images');
        }

        if ($request->hasFile('cv')) {
            $user->cv = $request->file('cv')->store('public/cvs');
        }

        $user->save();

        return response()->json($user);
    }

    public function updateJobOwner(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'job_owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'image' => 'nullable|image|max:2048',
        ]);

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');

        if ($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        if ($request->hasFile('image')) {
            $user->image = $request->file('image')->store('public/images');
        }

        $user->save();

        return response()->json($user);
    }

    public function updateCompany(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'company') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'image' => 'nullable|image|max:2048',
        ]);

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');

        if ($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        if ($request->hasFile('image')) {
            $user->image = $request->file('image')->store('public/images');
        }

        $user->save();

        return response()->json($user);
    }

}
