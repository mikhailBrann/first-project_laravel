<?php
use Illuminate\Support\Facades\Route;

Route::middleware("auth:admin")->group(function() {
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::get('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
});

Route::get('/', [\App\Http\Controllers\Admin\AuthController::class, 'index'])->name('main');
Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'loginForm'])->name('login');
Route::post('login_process', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login_process');

Route::get(
    'registration',
    [\App\Http\Controllers\Admin\AuthController::class, 'showRegisterForm']
)->name('registration');
Route::post(
    'registration_process',
    [\App\Http\Controllers\Admin\AuthController::class, 'register']
)->name('registration_process');

