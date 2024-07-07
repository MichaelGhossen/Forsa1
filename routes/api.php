    <?php

use App\Http\Controllers\API\Auth\AccountController;
use App\Http\Controllers\API\Auth\CategoryController;
use App\Http\Controllers\API\Auth\CvController;
use App\Http\Controllers\API\Auth\DeleteController;
use App\Http\Controllers\API\Auth\FavoriteController;
use App\Http\Controllers\API\Auth\FavoriteFreelanceController;
use App\Http\Controllers\API\Auth\JobController;
use App\Http\Controllers\API\Auth\JobsForFreelancersController;
use App\Http\Controllers\API\Auth\LoginController;
    use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\OrderController;
use App\Http\Controllers\API\Auth\OrderForFreelanceController;
use App\Http\Controllers\API\Auth\ShowController;
use App\Http\Controllers\API\Auth\SkillController;
use App\Http\Controllers\API\Auth\UpdateController;
use App\Http\Controllers\API\RegisterController;
    use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VerificationController;
use App\Models\OrderForFreelance;
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
        Route::post('job_owner', [RegisterController::class, 'registerJobOwner']);
        Route::post('company', [RegisterController::class, 'registerCompany']);
    });

    Route::group(['prefix' => 'login'], function () {
        Route::post('admin', [LoginController::class, 'loginAdmin']);
        Route::post('job_seeker', [LoginController::class, 'loginJobSeeker']);
        Route::post('job_owner', [LoginController::class, 'loginJobOwner']);
        Route::post('company', [LoginController::class, 'loginCompany']);
    });
    Route::middleware('auth:sanctum')->post('/users/choose-skills', [UserController::class, 'chooseSkills']);
    Route::middleware('auth:sanctum')->get('/user/skills/{id}', [SkillController::class, 'getUserSkills']);

    Route::middleware('auth:sanctum')->group(function () {
    Route::get('show/admin', [ShowController::class, 'showAdmin']);
    Route::get('show/job_seeker', [ShowController::class, 'showJObSeeker']);
    Route::get('show/job_owner', [ShowController::class, 'showJobOwner']);
    Route::get('show/company', [ShowController::class, 'showCompany']);
    Route::get('show/all/users', [ShowController::class, 'getAllUsers']);
    Route::get('show/all/companies', [ShowController::class, 'getAllCompanies']);
    Route::get('show/user/{id}', [ShowController::class, 'showUserById']);
    Route::get('show/company/{id}', [ShowController::class, 'showCompanyById']);
    Route::get('show/flagForUser/{id}', [ShowController::class, 'getFlagByUserId']);

});
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => 'logout'], function () {
            Route::post('admin', [LogoutController::class, 'logoutAdmin']);
            Route::post('job_seeker', [LogoutController::class, 'logoutJobSeeker']);
            Route::post('job_owner', [LogoutController::class, 'logoutJobOwner']);
            Route::post('company', [LogoutController::class, 'logoutCompany']);
        });
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/update/job_seeker', [UpdateController::class, 'updateJobSeeker']);
        Route::post('/update/job_owner', [UpdateController::class, 'updateJobOwner']);
        Route::post('/update/flag/{id}', [UpdateController::class, 'updateFlag']);
        Route::post('/update/company/flag/{id}', [UpdateController::class, 'updateFlagCompany']);

    });
Route::middleware('auth:sanctum')->group(function () {
Route::post('/delete/job_seeker', [DeleteController::class, 'deleteJobseeker']);
Route::post('/delete/job_owner', [DeleteController::class, 'deleteJobowner']);
Route::post('/delete/company', [DeleteController::class, 'deleteCompany']);
Route::post('/delete/job_seeker/{id}', [DeleteController::class, 'deleteJobSeekerById']);
Route::post('/delete/job_owner/{id}', [DeleteController::class, 'deleteJobOwnerById']);
Route::post('/delete/company/{id}', [DeleteController::class, 'deleteCompanyById']);

});
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/jobs', [JobController::class,'index']);
    Route::get('/job/{id}',[ JobController::class, 'show']);
    Route::post('/job/create', [JobController::class,'create']);
    Route::post('/job/update/{id}', [JobController::class,'update']);
    Route::post('/job/delete/{id}', [JobController::class,'destroy']);
    Route::get('/jobs/company/{id}', [JobController::class,'jobsByCompany']);
    Route::get('/jobs/admin/{id}', [JobController::class,'getJobsByUserId']);
    Route::get('/jobs/company/category/{company_id}/{category_id}', [JobController::class,'getJobsByFilters']);
    Route::get('/jobs/admin/category/{admin}/{category_id}', [JobController::class,'getJobsByAdminCategory']);
    Route::post('/job/SearchByCompanyId', [JobController::class,'searchJobByCompanyId']);
    Route::post('/job/SearchByAdminId', [JobController::class,'searchJobByAdminId']);

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
    Route::get('/job/freelancersByJobOwner/{id}',[ JobsForFreelancersController::class, 'getJobsByJobOwnerId']);
    Route::get('/job/JobsFreelanceByJobOwnerAndCategroyId/{user_id}/{category_id}',[ JobsForFreelancersController::class, 'getJobsFreelanceByJobOwnerAndCategroyId']);
    Route::post('/job/searchJobFreelanceByOwnerId', [JobsForFreelancersController::class,'searchJobByOwnerId']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/skills', [SkillController::class,'index']);
    Route::get('/skill/{id}',[ SkillController::class, 'show']);
    Route::post('/skill/create', [SkillController::class,'store']);
    Route::post('/skill/update/{id}', [SkillController::class,'update']);
    Route::post('/skill/delete/{id}', [SkillController::class,'destroy']);
});

    Route::get('/cvs', [CvController::class,'index']);
    Route::get('/cv/{id}',[ CvController::class, 'show']);
    Route::post('/cv/create', [CvController::class,'store']);
    Route::post('/cv/update/{id}', [CvController::class,'update']);
    Route::post('/cv/delete/{id}', [CvController::class,'destroy']);
//Routes for searches
Route::post('jobs/search', [JobController::class,'searchJob']);
Route::post('jobs/freelancer/search', [JobsForFreelancersController::class,'searchJobFreelancer']);
Route::post('category/search', [CategoryController::class,'searchCategory']);
Route::post('skill/search', [SkillController::class,'searchSkill']);
Route::get('/cvs/company/{companyId}', [CvController::class, 'getCvsByCompanyId']);
Route::get('/cvs/job_owner/{job_owner_id}', [CvController::class, 'getCvsByjobOwnerId']);
Route::post('/update/company/{id}', [UpdateController::class, 'updateCompany']);

Route::middleware('auth:sanctum')->group(function () {
Route::post('/favorites/add', [FavoriteController::class, 'store']);
Route::post('/favorites/delete', [FavoriteController::class, 'destroy']);
Route::post('/favorites/freelance/add', [FavoriteFreelanceController::class, 'store']);
Route::post('/favorites/freelance/delete', [FavoriteFreelanceController::class, 'destroy']);
Route::get('/users/favorites/{userId}', [FavoriteController::class,'getAllFavorites']);
Route::get('/freelancers/favorites/{userId}', [FavoriteFreelanceController::class,'getAllFavorites']);
Route::post('favorite/search', [FavoriteController::class,'searchJobInFavorites']);
Route::post('favorite/freelance/search', [FavoriteFreelanceController::class,'searchFavoriteForFreelance']);

});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class,'index']);
    Route::post('/order/create', [OrderController::class, 'store']);
    Route::post('/order/update/{id}', [OrderController::class,'update']);
    Route::post('/order/delete/{id}', [OrderController::class,'destroy']);
    Route::get('/order/company/{id}', [OrderController::class,'getOrdersByCompanyId']);
    Route::get('/order/show/{id}', [OrderController::class,'show']);
    Route::get('/order/user_id/{id}', [OrderController::class,'getAllOrders']);
    Route::get('/order/company_id/job_id/{company_id}/{job_id}', [OrderController::class,'getOrdersByCompanyAndJobId']);
    Route::get('/orders/status/{status?}', [OrderController::class, 'getOrdersByStatus']);

});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/accounts', [AccountController::class,'index']);
    Route::post('/account/create', [AccountController::class,'store']);
    Route::get('/account/show/{id}', [AccountController::class,'show']);
    Route::post('/account/update/{id}', [AccountController::class,'update']);
    Route::post('/account/delete/{id}', [AccountController::class,'destroy']);
});
Route::get('/jobs/category/{category_id}', [CategoryController::class,'getAllJobsByCategory_id']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/freelance/orders', [OrderForFreelanceController::class,'index']);
    Route::post('/freelance/apply', [OrderForFreelanceController::class, 'createOrder']);
    Route::post('/freelance/order/update/{id}', [OrderForFreelanceController::class,'update']);
    Route::post('/freelance/order/delete/{id}', [OrderForFreelanceController::class,'destroy']);
    Route::get('/freelance/order/job_owner/{id}', [OrderForFreelanceController::class,'getOrdersByJobOwnerId']);
    Route::get('/freelance/order/show/{id}', [OrderForFreelanceController::class,'show']);
    Route::get('/freelance/order/user_id/{id}', [OrderForFreelanceController::class,'getAllOrdersForUser']);
    Route::get('/freelance/orders/job_owner_id/jobForFreelance_id/{job_owner_id}/{j_obs_for_freelancers_id}', [OrderForFreelanceController::class,'getOrdersByJobOwnerAndJobForFreelanceId']);
    Route::get('/orders/status/{status?}', [OrderForFreelanceController::class, 'getOrdersByStatus']);

});
Route::post('email/verify/send',[VerificationController::class,'sendMail']);

Route::middleware('auth:sanctum')->get('/job_owners_for_admin', [UserController::class, 'getAllJobOwners']);
Route::middleware('auth:sanctum')->get('/job_seekers_for_admin', [UserController::class, 'getAllJobSeekers']);
Route::get('/get/picture/{id}', [RegisterController::class,'getFile']);
Route::get('/get/cv/{id}', [CvController::class,'getCv']);
Route::get('/get/cvByUser_id/{id}', [CvController::class,'getCvIdByUserId']);


Route::post('/jobOwner/user_id/{id}', [UserController::class,'getJobOwnerIdByUserId']);
