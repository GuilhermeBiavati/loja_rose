<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



// Route::get('/', [CategoryController::class, 'index']);
// Route::get('/{categorie}', [CategoryController::class, 'show']);
// Route::post('/', [CategoryController::class, 'store']);
// Route::put('/{categorie}', [CategoryController::class, 'update']);
// Route::delete('/', [CategoryController::class, 'delete']);

Route::apiResource('categories', CategoryController::class)->middleware('client');
Route::apiResource('brands', BrandController::class)->middleware('client');
Route::apiResource('products', ProductController::class)->middleware('client');

Route::apiResource('categories', CategoryController::class)->only('index', 'show');
Route::apiResource('brands', BrandController::class)->only('index', 'show');
Route::apiResource('products', ProductController::class)->only('index', 'show');
