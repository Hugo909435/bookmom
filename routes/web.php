<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);



});

Route::middleware(['auth', 'verified'])->group(function () {
    // Remplace la route dashboard par books
    Route::get('/dashboard', function () {
        return redirect()->route('books.index');
    })->name('dashboard');

    Route::resource('books', BookController::class);
    Route::resource('movies', MovieController::class);
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
