<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecyclingCenterController;
use App\Http\Controllers\OwnerController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// })->name('welcome');

Route::get('/', function () {
    return view('map');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('center', RecyclingCenterController::class);
    Route::resource('owner', OwnerController::class);
    Route::resource('branch', RecyclingCenterController::class);
});

require __DIR__.'/auth.php';
