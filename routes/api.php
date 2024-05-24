    <?php

use App\Http\Controllers\API\Auth\CategoryController;
use App\Http\Controllers\API\Auth\CvController;
use App\Http\Controllers\API\Auth\DeleteController;
use App\Http\Controllers\API\Auth\JobController;
use App\Http\Controllers\API\Auth\JobsForFreelancersController;
use App\Http\Controllers\API\Auth\LoginController;
    use App\Http\Controllers\API\Auth\LogoutController;
    use App\Http\Controllers\API\Auth\ShowController;
use App\Http\Controllers\API\Auth\SkillController;
use App\Http\Controllers\API\Auth\UpdateController;
use App\Http\Controllers\API\RegisterController;
    use App\Http\Controllers\API\UserController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider and all of them will
    | be assigned to the "api" middleware group. Make something great!
    |
    */

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('login', [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('user', [UserController::class, 'userDetails']);
        Route::get('logout', [LogoutController::class, 'logout']);
    });

    Route::group(['prefix' => 'register'], function () {
        Route::post('job_seeker', [RegisterController::class, 'registerJobSeeker']);
       // Route::post('freelancer', [RegisterController::class, 'registerFreelancer']);
        Route::post('job_owner', [RegisterController::class, 'registerJobOwner']);
        Route::post('company', [RegisterController::class, 'registerCompany']);
    });

    Route::group(['prefix' => 'login'], function () {
        Route::post('admin', [LoginController::class, 'loginAdmin']);
        Route::post('job_seeker', [LoginController::class, 'loginJobSeeker']);
        Route::post('job_owner', [LoginController::class, 'loginJobOwner']);
       // Route::post('freelancer', [LoginController::class, 'loginFreelancer']);
        Route::post('company', [LoginController::class, 'loginCompany']);
    });

   // Route::middleware('auth:sanctum')->get('/show/freelancer', [ShowController::class, 'showFreelancer']);
    Route::middleware('auth:sanctum')->get('show/admin', [ShowController::class, 'showAdmin']);
    Route::middleware('auth:sanctum')->get('show/job_seeker', [ShowController::class, 'showJObSeeker']);
    Route::middleware('auth:sanctum')->get('show/job_owner', [ShowController::class, 'showJobOwner']);
    Route::middleware('auth:sanctum')->get('show/company', [ShowController::class, 'showCompany']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => 'logout'], function () {
            Route::post('admin', [LogoutController::class, 'logoutAdmin']);
            Route::post('job_seeker', [LogoutController::class, 'logoutJobSeeker']);
            Route::post('job_owner', [LogoutController::class, 'logoutJobOwner']);
        //    Route::post('freelancer', [LogoutController::class, 'logoutFreelancer']);
            Route::post('company', [LogoutController::class, 'logoutCompany']);
        });
    });
    Route::middleware('auth:sanctum')->group(function () {
  //      Route::post('/update/freelancer', [UpdateController::class, 'updateFreelancer']);
        Route::post('/update/job_seeker', [UpdateController::class, 'updateJobSeeker']);
        Route::post('/update/job_owner', [UpdateController::class, 'updateJobOwner']);
        // Route::post('/update/company', [UpdateController::class, 'updateCompany']);
    });
Route::middleware('auth:sanctum')->group(function () {
Route::post('/delete/job_seeker', [DeleteController::class, 'deleteJobseeker']);
Route::post('/delete/job_owner', [DeleteController::class, 'deleteJobowner']);
//Route::post('/delete/freelancer', [DeleteController::class, 'deleteFreelancer']);
Route::post('/delete/company', [DeleteController::class, 'deleteCompany']);
});
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/jobs', [JobController::class,'index']);
    Route::get('/job/{id}',[ JobController::class, 'show']);
    Route::post('/job/create', [JobController::class,'create']);
    Route::post('/job/update/{id}', [JobController::class,'update']);
    Route::post('/job/delete/{id}', [JobController::class,'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/category', [CategoryController::class,'index']);
    Route::get('/category/{id}',[ CategoryController::class, 'show']);
    Route::post('/category', [CategoryController::class,'create']);
    Route::post('/category/update/{id}', [CategoryController::class,'update']);
    Route::post('/category/delete/{id}', [CategoryController::class,'destroy']);
});
//apiS for job Freelancers
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/jobs/freelancers', [JobsForFreelancersController::class,'index']);
    Route::get('/job/freelancers/{id}',[ JobsForFreelancersController::class, 'show']);
    Route::post('/job/freelancers/create', [JobsForFreelancersController::class,'create']);
    Route::post('/job/freelancers/update/{id}', [JobsForFreelancersController::class,'update']);
    Route::post('/job/freelancers/delete/{id}', [JobsForFreelancersController::class,'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/skills', [SkillController::class,'index']);
    Route::get('/skill/{id}',[ SkillController::class, 'show']);
    Route::post('/skill/create', [SkillController::class,'store']);
    Route::post('/skill/update/{id}', [SkillController::class,'update']);
    Route::post('/skill/delete/{id}', [SkillController::class,'destroy']);
});

//Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cvs', [CvController::class,'index']);
    Route::get('/cv/{id}',[ CvController::class, 'show']);
    Route::post('/cv/create', [CvController::class,'store']);
    Route::post('/cv/update/{id}', [CvController::class,'update']);
    Route::post('/cv/delete/{id}', [CvController::class,'destroy']);
//});
//Routes for searches
Route::post('jobs/search', [JobController::class,'searchJob']);
Route::post('jobs/freelancer/search', [JobsForFreelancersController::class,'searchJobFreelancer']);
Route::post('category/search', [CategoryController::class,'searchCategory']);
Route::post('skill/search', [SkillController::class,'searchSkill']);
Route::get('/cvs/company/{companyId}', [CvController::class, 'getCvsByCompanyId']);





Route::post('/update/company/{id}', [UpdateController::class, 'updateCompany']);
