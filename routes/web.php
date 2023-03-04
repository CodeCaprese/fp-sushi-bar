<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, "index"])
    ->name("home.index");
Route::post('/store', [HomeController::class, "store"])
    ->name("home.store");

Route::middleware(['check.init'])->group(function () {
    Route::prefix('table')->group(function () {
        Route::get('/', [TableController::class, "index"])
            ->name("table.index");
        Route::post('seat', [TableController::class, "seat"])
            ->name("table.seat");
        Route::post('billing', [TableController::class, "billing"])
            ->name("table.billing");
    });
});
