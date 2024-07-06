<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\CV;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
        $cvs = CV::get(['id']);
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
            'company_id' => 'nullable|integer',
            'cv' => 'required|mimes:pdf',
        ]);

        // Check if the user already has a CV
        $existingCV = CV::where('user_id', $request->user_id)->first();
        if ($existingCV) {
            return response()->json([
                'message' => 'User already has a CV. Cannot create another.'
            ], 400);
        }

        $imagePath = null;
        if ($request['cv'] != null) {
            $file = $request->file('cv');
            $imagePath = 'cvs/' . time() . $file->getClientOriginalName();
            Storage::disk('public')->put($imagePath, File::get($file));
        }
        $cv = CV::create([
            'user_id' => $request->user_id,
            'company_id' => $request->company_id,
            'file_path' => $imagePath,
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
            'cv' => 'required|mimes:pdf',
        ]);


        $file_path = null;
        if ($request['cv'] != null) {
            $file = $request->file('cv');
            $file_path = 'cvs/' . time() . $file->getClientOriginalName();
            Storage::disk('public')->put($file_path, File::get($file));
        }
        // $file_path = $request->file('cv')->store('cvs');

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
        } catch (Exception $e) {
            return response()->json(['error' => 'CV not found.'], 404);
        }
    }

    public function getCvsByCompanyId($companyId)
    {

        $cvs = Cv::where('company_id', $companyId)->get(['id']);
        return response()->json(['message' => $cvs], 200);
    }

    public function getCvsByJobOwnerId($job_owner_id)
    {
        $cvs = Cv::where('job_owner_id', $job_owner_id)->get(['id']);
        return response()->json(['message' => $cvs], 200);
    }

    public function getCv($id)
    {
        try {
            $file = CV::where('id', $id)->first();
            if ($file != null) {
                $responseFile = Storage::disk('public')->get($file['file_path']);
                return (new Response($responseFile, 200))
                    ->header('Content-Type', 'pdf');
            }
        } catch (Exception $e) {
            return response()->json([
                "data" => $e->getMessage()
            ]);
        }
    }
    public function getCvIdByUserId($userId)
    {
        try {
            // Retrieve the CV by user_id
            $cv = CV::where('user_id', $userId)
                 ->firstOrFail([
                     'id',
                 ]);

            // Return the cv_id as a JSON response
            return response()->json(['cv_id' => $cv->id], 200);
        } catch (ModelNotFoundException $e) {
            // Handle the case when the CV is not found
            return response()->json(['error' => 'CV not found'], 404);
        }
    }
}
