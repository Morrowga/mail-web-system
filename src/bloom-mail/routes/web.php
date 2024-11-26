<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SpamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TemplateCategoryController;

if (config('app.env') === 'production') {
    \Illuminate\Support\Facades\URL::forceScheme('https');
}
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/connection-error', function(){
    return Inertia::render('Errors/ConnectionError');
})->name('connection-error');

Route::middleware(['auth'])->group(function () {

    // <------------------ Mail System --------------------->

    Route::get('/inbox',[MailController::class, 'index'])->name('inbox');
    Route::get('/home',[DashboardController::class, 'index'])->name('dashboard');
    Route::post('/mails',[MailController::class, 'store'])->name('mails.store');
    Route::post('/mails/reply-forward/{mail_log}',[MailController::class, 'replyForward'])->name('mails.reply-forward');
    Route::delete('/mails/delete/{mail_log}',[MailController::class, 'destroy'])->name('mails.delete');
    Route::delete('/mails/sent/delete/{sent_mail}',[MailController::class, 'sentDestroy'])->name('mails.sent.delete');


    Route::get('/templates', function () {
        return Inertia::render('Templates/Index');
    })->name('templates');

    Route::resource('folders', FolderController::class);
    Route::resource('templates', TemplateController::class);
    Route::resource('template-categories', TemplateCategoryController::class);
    Route::resource('spams', SpamController::class);

    // <------------------ Mail System --------------------->

    // <------------------ App System --------------------->
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UserController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // <------------------ App System --------------------->

});

require __DIR__.'/auth.php';
require __DIR__.'/api.php';
