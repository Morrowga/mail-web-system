<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\SpamController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\Axio\MailController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\TemplateCategoryController;

Route::prefix('sys')->group(function () {
    Route::post('/registration', [AuthController::class, 'registration']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/products', [ProductController::class, 'index']);
    });
});
