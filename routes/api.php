<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthAdminController;
use App\Http\Controllers\Api\EmployerController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\VendorController;
use Illuminate\Support\Facades\Route;

// ========================
// User Authentication Routes
// ========================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected User Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);


});

// ========================
// Admin Authentication Routes
// ========================
Route::post('/admin/register', [AuthAdminController::class, 'register']);
Route::post('/admin/login', [AuthAdminController::class, 'login']);

// Protected Admin Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [AuthAdminController::class, 'logout']);
    Route::get('/admin', [AuthAdminController::class, 'admin']);
});


Route::apiResource('/employers', EmployerController::class);
Route::apiResource('/vendors', VendorController::class);
Route::apiResource('/packages', PackageController::class);
Route::apiResource('/subscriptions', SubscriptionController::class);

