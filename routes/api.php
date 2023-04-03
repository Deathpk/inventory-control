<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Brand\BrandController;
use App\Http\Controllers\BuyListController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Reports\InventorySubtractionController;
use App\Http\Controllers\Reports\SalesReportController;
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
        Route::post('/invite-employee', 'inviteEmployee');
        Route::delete('/delete-token/{tokenId}', 'revokeApiToken');
        Route::post('/register', 'register')->withoutMiddleware('auth:sanctum');
        Route::post('/login', 'login')->withoutMiddleware('auth:sanctum');
        Route::post('/logout', 'logout');
        Route::post('/change-password', 'changePassword');
        Route::post('/recover-password', 'recoverPassword')->withoutMiddleware('auth:sanctum');
        Route::post('/confirm-recovery', 'confirmPasswordRecovery')->withoutMiddleware('auth:sanctum');
});

Route::controller(InventorySubtractionController::class)->middleware('auth:sanctum')
    ->prefix('reports/inventory-sub')->group(function() {
        Route::get('/', 'index');
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
    Route::post('/remove-quantity', 'removeFromInventory');
    Route::get('/{productId}', 'show');
});

Route::controller(SalesController::class)->middleware('auth:sanctum')
    ->prefix('sales')->group(function () {
        Route::post('/sell', 'removeSoldUnits');
});

Route::controller(SalesReportController::class)->middleware('auth:sanctum')
    ->prefix('reports/sales')->group(function () {
        Route::get('/', 'index');
        Route::get('/most-sold', 'mostSoldProduct');
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

Route::controller(BuyListController::class)->middleware('auth:sanctum')
    ->prefix('buy-list')->group(function () {
        Route::get('/', 'index');
        Route::post('/add-item', 'store');
        Route::put('/edit', 'update');
        Route::delete('/remove', 'destroy');
});

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Pagina não encontrada'
    ], 404);
});

