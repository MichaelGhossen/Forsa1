<?php
namespace App\Http\Controllers\API\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Companies;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
class UpdateController extends Controller
{
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
    public function updateCompany(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies,email,'. $id,
            'password' => 'nullable|string|min:8', // Assuming password is optional
            'user_type' => 'required|string'
        ]);

        // Retrieve the company by ID
        $company = Companies::findOrFail($id);

        // Check if a new password has been provided
        if ($request->has('password')) {
            // Hash the new password
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        // Update the company attributes
        $company->fill($validatedData);
        $company->save();

        return response()->json($company, 200);
    }

}
