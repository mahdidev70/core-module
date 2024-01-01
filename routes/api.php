<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use TechStudio\Core\app\Http\Controllers\ReportController;
use TechStudio\Core\app\Http\Controllers\SearchController;

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
    Route::get('/users/search', [SearchController::class, 'searchUser']);
});
