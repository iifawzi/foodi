<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')
    // we could create separate routers files, if there will be more routes.
    ->group(function (){
        Route::prefix('orders')->name('orders')->group(function () {
            Route::post('/', [OrdersController::class, 'createOrder'])
                ->name('create');
        });
    });
