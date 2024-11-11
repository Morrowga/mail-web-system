<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\SpamController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\Axio\MailController;
use App\Http\Controllers\TemplateCategoryController;

Route::middleware(['auth'])->group(function () {
    Route::get('mails/inbox', [MailController::class, 'index'])
        ->name('inbox-mails');

    Route::post('mails/mark-as-read/{uid}', [MailController::class, 'markAsRead'])
        ->name('inbox-mark-as-read');
});
