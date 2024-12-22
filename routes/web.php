<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecyclingCenterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Route::get('/', function () {
//     return view('welcome');
// })->name('welcome');

Route::get('/', function () {
    return view('home');
})->name('welcome');

Route::get('/map', function () {
    return view('map');
})->name('map');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::get('/info', function () {
    return view('info');
})->name('info');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::prefix('contributor')->group(function () {
    Route::get('register', function () {
        return view('auth.registerUser');
    })->name('contributor.register');

    Route::post('register', [RegisteredUserController::class, 'storeContributor']);
});

Route::get('/api/locations', [RecyclingCenterController::class, 'getLocations']);
Route::get('/api/search', [RecyclingCenterController::class, 'search'])->name('searchLocations');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('center', RecyclingCenterController::class);
    Route::resource('admin', AdminController::class);
    Route::resource('owner', OwnerController::class);
    Route::resource('review', ReviewController::class);

    Route::get('review/{id}/fast', [ReviewController::class, 'fastReview'])->name('review.fast');
    Route::post('review/{id}/fast', [ReviewController::class, 'storeFastReview'])->name('review.fastStore');

    Route::put('center/{id}/verify', [RecyclingCenterController::class, 'verify'])->name('center.verify');
    Route::put('owner/{id}/verify', [OwnerController::class, 'verify'])->name('owner.verify');
});

require __DIR__ . '/auth.php';
