<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/testEmail', function () {
    return (new \App\Mail\LowStockNotification([["ingredientId" => 1, "ingredientName" => "lol", "current" => 1, "threshold" => 1]], "fawzi"))->render();
});
