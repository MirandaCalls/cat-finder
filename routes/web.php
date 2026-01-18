<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User management
    Route::get('/settings/users', [UserController::class, 'index'])->name('settings.users.index');
    Route::post('/settings/users', [UserController::class, 'store'])->name('settings.users.store');
    Route::delete('/settings/users/{user}', [UserController::class, 'destroy'])->name('settings.users.destroy');
});

require __DIR__.'/auth.php';
