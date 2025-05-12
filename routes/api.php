<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ {
    MenuCategoryController,
    MenuItemController,
    UserController,
    MenuController,
    OrderController,
};

use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('categories', MenuCategoryController::class);
    Route::apiResource('items', MenuItemController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('menus', MenuController::class);
    Route::apiResource('orders', OrderController::class);
});
