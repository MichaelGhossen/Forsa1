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
public function getAllJobsByCategory_id($category_id, Request $request)
{
    $jobs1 = Job::where('category_id', $category_id)->get();
    $jobs2 = JobsForFreelancers::where('category_id', $category_id)
        ->with('skills')
        ->get();

    if ($request->has('skills')) {
        $jobs2 = $jobs2->filter(function ($job) use ($request) {
            $jobSkills = $job->skills->pluck('id')->toArray();
            return count(array_intersect($request->get('skills'), $jobSkills)) === count($request->get('skills'));
        });
    }

    $response = [
        'jobs' => $jobs1->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'min_duration'=>$job->min_duration,
                'max_duration'=>$job->max_duration ,
                'min_age'=>$job->min_age ,
                'max_age'=>$job->max_age ,
                'min_salary'=>$job->min_salary ,
                'max_salary'=>$job->max_salary ,
                'gender'=>$job->gender ,
                'languages'=>$job->languages ,
                'description'=>$job->description ,
                'category_id' => $job->category_id,
                'location'=>$job->location ,
                'company_id'=>$job->company_id ,
                'user_id'=>$job->user_id ,
            ];
        }),
        'jobs_for_freelancers' => $jobs2->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'min_duration' => $job->min_duration,
                'max_duration' => $job->max_duration,
                'salary' => $job->salary,
                'languages' => $job->languages,
                'description' => $job->description,
                'category_id' => $job->category_id,
                'user_id' => $job->user_id,
                'skills' => $job->skills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'description' => $skill->description,
                    ];
                })->toArray(),
            ];
        }),
    ];

    return response()->json($response);
}
}
