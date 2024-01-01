<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use TechStudio\Blog\app\Http\Controllers\ArticleController;
use TechStudio\Community\app\Http\Controllers\ChatRoomController;
use TechStudio\Community\app\Http\Controllers\QuestionController;
// use TechStudio\Community\app\Http\Controllers\SearchController;
use TechStudio\Core\app\Http\Controllers\CommentController;
use TechStudio\Core\app\Http\Controllers\ReportController;
use TechStudio\Core\app\Http\Controllers\SearchController;
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

Route::get('report/list', [ReportController::class,'list']);

Route::middleware("auth:sanctum")->group(function () {

    Route::prefix('panel')->group(function (){

        Route::prefix('/users')->group(function() {

            Route::get('general',[UserProfileController::class,'createUserCommon']);
            Route::get('data', [UserProfileController::class,'getUsersListData'])/*->can('read_users')*/;
            Route::get('common', [UserProfileController::class,'getUsersListCommon']);
            Route::put('set_roles', [UserProfileController::class,'setRoles'])/*->can('set_user_roles')*/;
            Route::put('set_status', [UserProfileController::class,'setStatus'])/*->can('set_user_status')*/;
            Route::post('create',[UserProfileController::class,'createUser'])/*->can('add_user')*/;
            Route::get('{user}/show',[UserProfileController::class,'editUser'])/*->can('show_user')*/;
            Route::post('{user}/update',[UserProfileController::class,'updateUser'])/*->can('edit_user')*/;
            Route::post('generatePassword',[UserProfileController::class,'generatePassword']);

        });

        Route::prefix('/user')->group(function () {

            Route::get('/data', [UserProfileController::class, 'getUserData']);
            Route::put('edit-data', [UserProfileController::class, 'editData']);
            Route::get('/comments/list', [CommentController::class, 'getUserComment']);
            Route::get('/posts/list', [ArticleController::class, 'getUserArticle']);
            Route::get('/courses/list', [CourseController::class, 'getUserCourse']);
            Route::get('/questions/list', [QuestionController::class, 'getUserQuestion']);
            Route::get('/rooms/list', [ChatRoomController::class, 'getUserRoom']);

        });

    });
    // ========== PANEL USERS ===============

    Route::get('/users/search', [SearchController::class,'searchUser']);

});
