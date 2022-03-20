<?php

use App\Http\Controllers\Autocomplete\AutocompleteController;
use App\Http\Controllers\Brand\BrandController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Product\ProductController;
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

Route::controller(ProductController::class)->prefix('products')->group(function() {
    Route::get('/', 'index');
    Route::post('/create', 'store');
    Route::put('/edit/{productId}', 'update');
    Route::delete('/delete/{productId}', 'destroy');
    Route::get('/autocomplete', 'autoComplete');
    Route::post('/import', 'import');
    Route::get('/{productId}', 'show');
});

Route::controller(CategoryController::class)->prefix('categories')->group(function () {
    Route::get('/', 'index');
    Route::post('/create', 'store');
    Route::put('/edit/{category}', 'update');
    Route::delete('/delete/{category}', 'destroy');
    Route::get('/autocomplete', 'autoComplete');
    Route::get('/{categoryId}', 'show');
});

Route::controller(BrandController::class)->prefix('brands')->group(function () {
    Route::get('/', 'index');
    Route::post('/create', 'store');
    Route::put('/edit/{brandId}', 'update');
    Route::delete('/delete/{brandId}', 'destroy');
    Route::get('/autocomplete', 'autoComplete');
    Route::get('/{brandId}', 'show');
});

