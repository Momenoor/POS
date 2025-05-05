<?php

use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('menu-categories', MenuCategoryController::class);
Route::apiResource('menu-items', MenuItemController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('menus', \App\Http\Controllers\MenuController::class);
Route::apiResource('orders', \App\Http\Controllers\OrderController::class);
