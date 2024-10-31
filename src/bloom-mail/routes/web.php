<?php

use App\Http\Controllers\ProfileController;
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

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/templates', function () {
        return Inertia::render('Templates/Index');
    })->name('templates');

    Route::get('/template-categories', function () {
        return Inertia::render('TemplateCategories/Index');
    })->name('template-categories');

    Route::get('/folders', function () {
        return Inertia::render('Folders/Index');
    })->name('folders');

    Route::get('/spams', function () {
        return Inertia::render('Mail/Spams/Index');
    })->name('spams');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
