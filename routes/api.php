<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use TechStudio\Core\app\Http\Controllers\TroubleShootingReportController;
use TechStudio\Core\app\Http\Controllers\VideoController;
use TechStudio\Blog\app\Http\Controllers\ArticleController;
use TechStudio\Community\app\Http\Controllers\ChatRoomController;
use TechStudio\Community\app\Http\Controllers\QuestionController;
use TechStudio\Core\app\Http\Controllers\BannerController;
use TechStudio\Core\app\Http\Controllers\CategoriesController;
// use TechStudio\Community\app\Http\Controllers\SearchController;
use TechStudio\Core\app\Http\Controllers\CommentController;
use TechStudio\Core\app\Http\Controllers\FaqController;
use TechStudio\Core\app\Http\Controllers\FollowController;
use TechStudio\Core\app\Http\Controllers\LandingController;
use TechStudio\Core\app\Http\Controllers\ReportController;
use TechStudio\Core\app\Http\Controllers\SearchController;
use TechStudio\Core\app\Http\Controllers\StaticController;
use TechStudio\Core\app\Http\Controllers\UserProfileController;
use TechStudio\Lms\app\Http\Controllers\CourseController;

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
// =================== CLIENT =====================

Route::get('static/index', [StaticController::class, 'index']);
Route::get('report/list', [ReportController::class, 'list']);

Route::prefix('faq')->group(function () {
    Route::get('list', [FaqController::class, 'list']);
    Route::get('common', [FaqController::class, 'common']);
});
// ============== STATIC LANDING ==================

Route::get('landing/first', [LandingController::class, 'first']);


Route::prefix('kns/user')->group(function () {
    Route::get('/posts', [ArticleController::class, 'knsPosts']);
    Route::get('/data', [UserProfileController::class, 'knsUserData']);
    Route::put('follow', [FollowController::class, 'storeRemove'])->middleware("auth:sanctum");
    Route::get('followers/list', [FollowController::class, 'followersList']);
    Route::get('following/list', [FollowController::class, 'followingList']);
});

Route::get('kns/search/{type}/{keyword}', [SearchController::class, 'searchData']);
Route::get('/general/search', [SearchController::class, 'generalSearch']);
// ===================== PANEL ====================
Route::middleware("auth:sanctum")->group(function () {

    Route::prefix('panel')->group(function () {

        Route::prefix('/video')->group(function () {
            Route::get('/{id}', [VideoController::class, 'show']);
            Route::get('/list/{page}', [VideoController::class, 'list']);
            Route::get('/search/{keyword}/{page}', [VideoController::class, 'search']);
        });

        Route::prefix('/users')->group(function () {
            Route::get('general', [UserProfileController::class, 'createUserCommon']);
            Route::get('data', [UserProfileController::class, 'getUsersListData'])/*->can('read_users')*/;
            Route::get('common', [UserProfileController::class, 'getUsersListCommon']);
            Route::put('set_roles', [UserProfileController::class, 'setRoles'])/*->can('set_user_roles')*/;
            Route::put('set_status', [UserProfileController::class, 'setStatus'])/*->can('set_user_status')*/;
            Route::post('create', [UserProfileController::class, 'createUser'])/*->can('add_user')*/;
            Route::get('{user}/show', [UserProfileController::class, 'editUser'])/*->can('show_user')*/;
            Route::post('{user}/update', [UserProfileController::class, 'updateUser'])/*->can('edit_user')*/;
            Route::post('generatePassword', [UserProfileController::class, 'generatePassword']);
        });

        Route::prefix('/user')->group(function () {
            Route::get('/data', [UserProfileController::class, 'getUserData']);
            Route::put('edit-data', [UserProfileController::class, 'editData']);
            Route::put('edit-data2', [UserProfileController::class, 'editData2']);
            Route::get('/comments/list', [CommentController::class, 'getUserComment']);
            Route::get('/posts/list', [ArticleController::class, 'getUserArticle']);
            Route::get('/courses/list', [CourseController::class, 'getUserCourse']);
            Route::get('/questions/list', [QuestionController::class, 'getUserQuestion']);
            Route::get('/rooms/list', [ChatRoomController::class, 'getUserRoom']);
            Route::get('/payment/list', [UserProfileController::class, 'getPaymentData']);
            Route::get('payment/installment/list', [UserProfileController::class, 'getInstallmentPaymentData']);
        });

        Route::prefix('category')->group(function () {
            Route::get('/list', [CategoriesController::class, 'categoryData']);
            Route::put('/edit-data', [CategoriesController::class, 'categoryEditData']);
            Route::put('/set-status', [CategoriesController::class, 'categorySetStatus']);
            Route::get('common-data', [CategoriesController::class, 'categoryCommon']);
        });

        Route::prefix('faq')->group(function () {
            Route::get('list', [FaqController::class, 'getFaqData']);
            Route::put('edit-data', [FaqController::class, 'createUpdate']);
            Route::put('set_status', [FaqController::class, 'setStatus']);
            Route::get('common', [FaqController::class, 'panelCommon']);
            Route::delete('{id}', [FaqController::class, 'delete']);
        });

        Route::prefix('static')->group(function () {
            Route::get('list', [StaticController::class, 'list']);
            Route::put('edit-data', [StaticController::class, 'createUpdate']);
            Route::delete('delete/{id}', [StaticController::class, 'delete']);
        });

        Route::prefix('banner')->group(function () {
            Route::get('list', [BannerController::class, 'list']);
            Route::put('edit-data', [BannerController::class, 'createUpdate']);
            Route::put('set-status', [BannerController::class, 'setStatus']);
            Route::get('common', [BannerController::class, 'common']);
        });
    });
    // ========== PANEL USERS ===============

    Route::prefix('troubleshooting-report')->group(function () {
        Route::post('/', [TroubleShootingReportController::class, 'store']);
    });

    Route::get('/users/search', [SearchController::class, 'searchUser']);
});
