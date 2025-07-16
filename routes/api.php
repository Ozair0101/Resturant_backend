<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MenuItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ResturantTableController;
use App\Models\MenuItem;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customer', [CustomerController::class, 'index']);
    Route::post('/customer', [CustomerController::class, 'store']);
    Route::get('/customer/{id}', [CustomerController::class, 'edit']);
    Route::put('/customer/{id}', [CustomerController::class, 'update']);
    Route::delete('/customer/{id}', [CustomerController::class, 'destroy']);


    Route::get('/category', [MenuItemController::class, 'index']);
    Route::post('/category', [MenuItemController::class, 'store']);

    Route::get('/menu-item', [MenuItemController::class, 'index']);
    Route::post('/menu-item', [MenuItemController::class, 'store']);
    Route::get('/menu-item/{id}', [MenuItemController::class, 'edit']);
    Route::put('/menu-item/{id}', [MenuItemController::class, 'update']);
    Route::delete('/menu-item/{id}', [MenuItemController::class, 'destroy']);


    Route::get('/restaurant-table', [ResturantTableController::class, 'index']);
    Route::post('/restaurant-table', [ResturantTableController::class, 'store']);
    Route::get('/restaurant-table/{id}', [ResturantTableController::class, 'edit']);
    Route::put('/restaurant-table/{id}', [ResturantTableController::class, 'update']);
    Route::delete('/restaurant-table/{id}', [ResturantTableController::class, 'destroy']);
});
