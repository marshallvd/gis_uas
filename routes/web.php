<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PolylineController;
use App\Http\Controllers\ApiController;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/home', function () {
    return view('frontend.home');
})->name('home');

// Rute untuk Polyline
Route::get('/polyline', [PolylineController::class, 'index'])->name('polyline.index');
Route::get('/polyline/create', [PolylineController::class, 'create'])->name('polyline.create');
Route::post('/polyline/store', [PolylineController::class, 'store'])->name('polyline.store');
Route::get('/polyline/{id}/edit', [PolylineController::class, 'edit'])->name('polyline.edit');
Route::put('/polyline/{id}', [PolylineController::class, 'update'])->name('polyline.update');
Route::delete('/polyline/{id}', [PolylineController::class, 'destroy'])->name('polyline.destroy');
Route::get('/polyline/{id}', [PolylineController::class, 'detail'])->name('polyline.detail');

// Route controller untuk otentikasi
Route::controller(AuthController::class)->group(function () {
    Route::get('register', 'register')->name('register');
    Route::post('register', 'registerSave')->name('register.save');
    
    Route::get('login', 'showLoginForm')->name('login');
    Route::post('login', 'login')->name('login.action');
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('web');
// Route yang memerlukan autentikasi
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    // Tambahkan route lain yang memerlukan autentikasi di sini
});