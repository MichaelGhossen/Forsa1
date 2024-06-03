<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\CV;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CvController extends Controller
{
    public function index()
{
    // Retrieve the authenticated user
    $user = auth()->user();

    // Check if the user is an admin
    if ($user && $user->user_type === 'admin') {
        return response()->json(['message' => 'You are not authorized to access this resource'], 403);
    }
    $cvs = CV::all();
    return response()->json($cvs, 200);
}


    /**
     * Store a newly created CV in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'company_id' => 'required|integer',
            'cv' => 'required|file',
        ]);
        $file_path = $request->file('cv')->store('cvs');
        $cv = CV::create([
            'user_id' => $request->user_id,
            'company_id' => $request->company_id,
            'file_path' => $file_path,
        ]);

        return response()->json($cv, 201);
    }
    /**
     * Display the specified CV.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cv = CV::findOrFail($id);
        return response()->json($cv);
    }
    /**
     * Update the specified CV in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cv' => 'required|file',
        ]);

        $file_path = $request->file('cv')->store('cvs');

        $cv = CV::findOrFail($id);
        $cv->update([
        'file_path' => $file_path,
        ]);
        return response()->json($cv);
    }
    /**
     * Remove the specified CV from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $cv = CV::findOrFail($id);
            $cv->delete();
            return response()->json(['message' => 'CV deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'CV not found.'], Response::HTTP_NOT_FOUND);
        }
    }

    public function getCvsByCompanyId($companyId)
    {

        $cvs = Cv::where('company_id', $companyId)->get();
        $arr[]=null;
        $i=0;
        foreach($cvs as $cvs)
     {  $arr[$i++]=$cvs;
}
        return response()->json(['message'=>$arr],200);
    }
}
