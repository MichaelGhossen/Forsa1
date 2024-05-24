<?php

namespace App\Http\Controllers\API\Auth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
class SkillController extends Controller
{
    public function index()
{
    $skills = Skill::all();
    $arr[]=null;
    $i=0;
    foreach($skills as $skills)
 {{   $arr[$i++]=$skills;
}}
    return response()->json(['message'=>$arr],200);
}
    /**
     * Store a newly created skill in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type === 'admin') {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $skill = Skill::create($validatedData);
        return response()->json($skill, Response::HTTP_CREATED);
    }
    else{
        return response()->json(['error' => 'You are not authorized to delete this job.'], 403);

    }

    }
    /**
     * Display the specified skill.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $skill = Skill::findOrFail($id);
        return response()->json($skill, Response::HTTP_OK);
    }

    /**
     * Update the specified skill in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->user_type === 'admin') {

        $skill = Skill::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $skill->update($validatedData);
        return response()->json($skill, Response::HTTP_OK);
    }
    else{
        return response()->json(['error' => 'You are not authorized to delete this job.'], 403);

    }
    }
    /**
     * Remove the specified skill from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->user_type === 'admin') {
            try {
                $skill = Skill::findOrFail($id);
                $skill->delete();
                return response()->json(['message' => 'Job deleted successfully']);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return response()->json(['error' => 'Skill not found.'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return response()->json(['error' => 'You are not authorized to delete this skill.'], Response::HTTP_FORBIDDEN);
        }
    }

    public function searchSkill( Request $request){

        $validator = Validator::make($request->all(),[
            'name'=>'required',
        ]);
        if(  $validator->fails()){
            return response()->json([
                'message'=>'Search Field required',
                'error'=> $validator->errors()
            ]);
        }
        $skills=Skill::where("name","like","%$request->name%")->first();
        if( $skills){
            return response()->json([
                'data'=> $skills->name,
            'message'=>'Found it',
            ],200);
        }
        else{
            return response()->json([
                'data'=>Null,
                'message'=>'No skills matched your search',
            ],404);
        }
        }
}
