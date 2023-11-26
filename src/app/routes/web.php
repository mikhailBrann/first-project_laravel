<?php

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

Route::get('/', [
    \App\Http\Controllers\IndexController::class,
    'index',
])->name('main');

//группа роутов с товарами
Route::get('/products', [
    \App\Http\Controllers\ProductController::class,
    'getList',
])->name('products.list');

Route::get('/products/{id}', [
    \App\Http\Controllers\ProductController::class,
    'getItem',
])->name('products.detail');


//группа роутов с авторизацией
Route::middleware("auth")->group(function() {
    Route::get('/logout', [
        \App\Http\Controllers\AuthController::class,
        'logout'
    ])->name('logout');
});

//группа роутов без авторизации
Route::middleware("guest")->group(function() {
    Route::get('/login', [
        \App\Http\Controllers\AuthController::class,
        'showLoginForm'
    ])->name('login');
    Route::post('/login_process', [
        \App\Http\Controllers\AuthController::class,
        'login'
    ])->name('login_process');

    Route::get('/registration', [
        \App\Http\Controllers\AuthController::class,
        'showRegisterForm'
    ])->name('registration');
    Route::post('/registration_process', [
        \App\Http\Controllers\AuthController::class,
        'register'
    ])->name('registration_process');

    //роуты для восстановления пароля
    Route::get('/forgot', [
        \App\Http\Controllers\AuthController::class,
        'showForgotForm'
    ])->name('forgot');
    Route::post('/forgot_process', [
        \App\Http\Controllers\AuthController::class,
        'forgot'
    ])->name('forgot_process');
});

//форма обратной связи
Route::get('/contact_form', [
    \App\Http\Controllers\IndexController::class,
    'getContactForm'
])->name('contact_form');

Route::post('/contact_form_process', [
    \App\Http\Controllers\IndexController::class,
    'contactForm'
])->name('contact_form_process');





