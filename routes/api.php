<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ResturantTableController;
use App\Http\Controllers\TableController;
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
    Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
    
    Route::get('/customer', [CustomerController::class, 'index']);
    Route::post('/customer', [CustomerController::class, 'store']);
    Route::get('/customer/{id}', [CustomerController::class, 'edit']);
    Route::put('/customer/{id}', [CustomerController::class, 'update']);
    Route::delete('/customer/{id}', [CustomerController::class, 'destroy']);

    Route::get('/menu-item', [MenuItemController::class, 'index']);
    Route::get('/menu-item/create', [MenuItemController::class, 'create']);
    Route::post('/menu-item', [MenuItemController::class, 'store']);
    Route::get('/menu-item/{id}', [MenuItemController::class, 'edit']);
    Route::put('/menu-item/{id}', [MenuItemController::class, 'update']);
    Route::delete('/menu-item/{id}', [MenuItemController::class, 'destroy']);

    Route::get('/order', [OrderController::class, 'index']);
    Route::get('/order/create', [OrderController::class, 'create']);
    Route::post('/order', [OrderController::class, 'store']);
    Route::put('/order/update-status/{id}', [OrderController::class, 'updateStatus']);
    Route::get('/order/{id}', [OrderController::class, 'edit']);
    Route::put('/order/{id}', [OrderController::class, 'update']);
    Route::delete('/order/{id}', [OrderController::class, 'destroy']);

    Route::get('/payment', [PaymentController::class, 'index']);
    Route::post('/payment', [PaymentController::class, 'store']);
    Route::get('/revenue', [PaymentController::class, 'revenue']);

    Route::get('/employee', [EmployeeController::class, 'index']);
    Route::post('/employee', [EmployeeController::class, 'store']);
    Route::get('/employee/{id}', [EmployeeController::class, 'edit']);
    Route::put('/employee/{id}', [EmployeeController::class, 'update']);
    Route::delete('/employee/{id}', [EmployeeController::class, 'destroy']);

    Route::get('/table', [TableController::class, 'index']);
    Route::get('/table/create', [TableController::class, 'create']);
    Route::post('/table', [TableController::class, 'store']);

    
});
