<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Job;
use App\Models\JObsForFreelancers;
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
        $arr[]=null;
        $i=0;
        foreach($categories as $categories)
     {  $arr[$i++]=$categories;
    }
        return response()->json(['message'=>$arr],200);
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
    public function searchCategory( Request $request){

        $validator = Validator::make($request->all(),[
            'name'=>'required',
        ]);
        if(  $validator->fails()){
            return response()->json([
                'message'=>'Search Field required',
                'error'=> $validator->errors()
            ]);
        }
        $category=Category::where("name","like","%$request->name%")->first();
        if( $category){
            return response()->json([
                'data'=> $category->name,
            'message'=>'Found it',
            ],200);
        }
        else{
            return response()->json([
                'data'=>Null,
                'message'=>'No categories matched your search',
            ],404);
        }
        }
//         public function getAllJobsByCategory_id($category_id)
// {
//     $jobs = Job::where('category_id', $category_id)->get();

//     return response()->json($jobs);
// }
public function getAllJobsByCategory_id($category_id)
{
    $jobs = Job::where('category_id', $category_id)->get();
    $jobsForFreelancers = JObsForFreelancers::where('category_id', $category_id)->get();

    $response = [
        'jobs' => $jobs,
        'jobs_for_freelancers' => $jobsForFreelancers
    ];

    return response()->json($response);
}
}

