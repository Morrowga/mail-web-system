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
    Route::get('mails/fetch', [MailController::class, 'index'])
        ->name('fetch-mails');

    Route::get('mails/histories/{id}', [MailController::class, 'getHistories'])
        ->name('histories');

    Route::post('mails/mark-as-read/{id}', [MailController::class, 'markAsRead'])
        ->name('inbox-mark-as-read');
});
