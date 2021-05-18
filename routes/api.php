<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => ''], function () {
    $expeptCreateAndEdit = ['except' => ['create', 'edit']];
    Route::resource('categories', CategoryController::class, $expeptCreateAndEdit);
    Route::resource('brands', BrandController::class, $expeptCreateAndEdit);
    Route::resource('products', ProductController::class, $expeptCreateAndEdit);
});
