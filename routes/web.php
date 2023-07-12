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


Route::group([
    'domain' => App::environment('local') ? 'openpims.test' : 'openpims.de'
], function () {
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware(['auth', 'verified']);
    Auth::routes(['register' => true, 'verify' => true]);
    Route::resource('site', 'App\Http\Controllers\SiteController')->middleware(['auth', 'verified']);
    Route::get('/setup', [App\Http\Controllers\SetupController::class, 'index'])->name('setup')->middleware(['auth', 'verified']);
    Route::post('/setup', [App\Http\Controllers\SetupController::class, 'index'])->name('setup')->middleware(['auth', 'verified']);
    Route::get('/export', [App\Http\Controllers\HomeController::class, 'export'])->name('export')->middleware(['auth', 'verified']);
    Route::get('/category/{site_id}', [App\Http\Controllers\HomeController::class, 'category'])->name('category')->middleware(['auth', 'verified']);
    Route::get('/consent/{standard}/{category_id}', [App\Http\Controllers\HomeController::class, 'consent'])->name('category')->middleware(['auth', 'verified']);
});


Route::group([
    'domain' => App::environment('local') ? '{token}.openpims.test' : '{token}.openpims.de'
], function () {
    //Route::resource('/{site?}', 'App\Http\Controllers\ApiController');
    Route::resource('/', 'App\Http\Controllers\ApiController');
});
