<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\DataCameraController;
use App\Http\Controllers\API\UserController;
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
Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {
    // USER
    Route::get('user', [UserController::class, 'get']);
    Route::get('all-user', [UserController::class, 'all']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('delete-user', [UserController::class, 'delete']);
    
    // DATA CAMERA
    Route::get('datacamera',[DataCameraController::class, 'all']);
    Route::post('datacamera',[DataCameraController::class, 'add']);
    Route::get('avg-weight-per-day',[DataCameraController::class, 'getAvgWeightPerDay']);
    Route::get('avg-weight-per-month',[DataCameraController::class, 'getAverageWeightPerMonth']);
    Route::get('avg-weight-per-specific-month',[DataCameraController::class, 'getAverageWeightForSpecificMonth']);
});
