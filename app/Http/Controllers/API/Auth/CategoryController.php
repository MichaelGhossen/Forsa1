<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
class CategoryController extends Controller
{
   /**
     * Display a listing of the categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        // Check if the user is a company or admin
        if ($user->user_type === 'admin') {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $category = Category::create($validatedData);
            
            return response()->json($category, 201);
        } else {
            return response()->json(['error' => 'You are not authorized to create a category.'], 403);
        }
    }
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Check if the user is a company or admin
        if ($user->user_type === 'admin') {
            $category = Category::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $category->update($validatedData);

            return response()->json($category);
        } else {
            return response()->json(['error' => 'You are not authorized to update a category.'], 403);
        }
    }


    public function destroy($id)
    {
        $user = Auth::user();

        // Check if the user is a company or admin
        if ($user->user_type === 'admin') {
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json(['message' => 'Category deleted successfully.']);
        } else {
            return response()->json(['error' => 'You are not authorized to delete a category.'], 403);
        }
    }
}

