<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Brand\BrandController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Sales\SalesController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->middleware('auth:sanctum')->prefix('auth')
    ->group(function () {
        Route::post('/register-token', 'registerApiToken');
        Route::delete('/delete-token/{tokenId}', 'revokeApiToken');
        Route::post('/register', 'register')->withoutMiddleware('auth:sanctum');
        Route::post('/login', 'login')->withoutMiddleware('auth:sanctum');
        Route::post('/logout', 'logout');
});

Route::controller(ProductController::class)->middleware('auth:sanctum')
    ->prefix('products')->group(function() {
    Route::get('/', 'index');
    Route::post('/create', 'store');
    Route::put('/edit', 'update');
    Route::delete('/delete', 'destroy');
    Route::get('/autocomplete', 'autoComplete');
    Route::post('/import', 'import');
    Route::post('/add-quantity', 'addToStock');
    Route::get('/{productId}', 'show');
});

Route::controller(SalesController::class)->middleware('auth:sanctum')
    ->prefix('sales')->group(function () {
        Route::post('/sell', 'removeSoldUnits');
});

Route::controller(CategoryController::class)->middleware('auth:sanctum')
    ->prefix('categories')->group(function () {
    Route::get('/', 'index');
    Route::post('/create', 'store');
    Route::put('/edit/{category}', 'update');
    Route::delete('/delete/{category}', 'destroy');
    Route::get('/autocomplete', 'autoComplete');
    Route::get('/{categoryId}', 'show');
});

Route::controller(BrandController::class)->middleware('auth:sanctum')
    ->prefix('brands')->group(function () {
    Route::get('/', 'index');
    Route::post('/create', 'store');
    Route::put('/edit/{brandId}', 'update');
    Route::delete('/delete/{brandId}', 'destroy');
    Route::get('/autocomplete', 'autoComplete');
    Route::get('/{brandId}', 'show');
});

