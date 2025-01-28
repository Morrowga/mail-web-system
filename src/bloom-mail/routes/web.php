<?php

use Inertia\Inertia;
use App\Repositories\MailRepository;
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
use App\Http\Controllers\System\MemberController;
use App\Http\Controllers\System\ProductController;
use App\Http\Controllers\TemplateCategoryController;
use App\Http\Controllers\Axio\MailController as MailAxioController;

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
    Route::post('/mails/redo/{mail_log}',[MailController::class, 'redo'])->name('mails.redo');
    Route::delete('/mails/delete-forever/{mail_log}',[MailController::class, 'destroyForever'])->name('mails.deleteforever');
    Route::delete('/mails/sent/delete/{sent_mail}',[MailController::class, 'sentDestroy'])->name('mails.sent.delete');


    Route::get('/templates', function () {
        return Inertia::render('Templates/Index');
    })->name('templates');

    Route::resource('folders', FolderController::class);
    Route::resource('templates', TemplateController::class);
    Route::resource('template-categories', TemplateCategoryController::class);
    Route::resource('spams', SpamController::class);

    // mail axios start

    Route::get('/mails/fetch', [MailAxioController::class, 'index']);

    Route::get('mails/fetch/folder/{folder}', [MailAxioController::class, 'indexWithFolderId'])
        ->name('fetch-mails-with-folder-id');

    Route::get('mails/histories/{id}', [MailAxioController::class, 'getHistories'])
        ->name('histories');

    Route::post('mails/change-reply/{mail_Log}', [MailAxioController::class, 'changeReply'])
        ->name('change-reply');

    Route::post('mails/cancel-status/{mail_Log}', [MailAxioController::class, 'cancelReply'])
        ->name('cancel-status');

    Route::post('mails/change-status/{mail_Log}', [MailAxioController::class, 'changeStatus'])
        ->name('change-status');

    Route::post('mails/folder-switch', [MailAxioController::class, 'folderSwitch']);

    // mail axios end

    // <------------------ Mail System --------------------->

    // <------------------ App System --------------------->
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UserController::class);
    Route::resource('members', MemberController::class);
    Route::resource('products', ProductController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // <------------------ App System --------------------->

});

require __DIR__.'/auth.php';
