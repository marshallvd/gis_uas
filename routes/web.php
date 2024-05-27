<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PolylineController;
use App\Http\Controllers\ApiController;
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

// Dashboard dan Home bisa diakses tanpa login
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
Route::get('/polyline/edit', [PolylineController::class, 'edit'])->name('polyline.edit');



// Route controller untuk otentikasi
Route::controller(AuthController::class)->group(function () {
    Route::get('register', 'register')->name('register');
    Route::post('register', 'registerSave')->name('register.save');

    Route::get('login', 'login')->name('login');
    Route::post('login', 'loginAction')->name('login.action');

    // Logout sebaiknya menggunakan metode POST
    Route::post('logout', 'logout')->middleware('auth')->name('logout');


});

// Mengamankan halaman profile dengan middleware 'auth'
Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
});



Route::get('/regions', [ApiController::class, 'getAllRegions']);
Route::get('/province/{id}', [ApiController::class, 'getProvinceById']);
Route::get('/kabupaten/province/{id}', [ApiController::class, 'getKabupatenByProvinceId']);
Route::get('/kecamatan/kabupaten/{id}', [ApiController::class, 'getKecamatanByKabupatenId']);
Route::get('/desa/kecamatan/{id}', [ApiController::class, 'getDesaByKecamatanId']);
