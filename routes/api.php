<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Support\Facades\Route;


Route::apiResource('articles', ArticleController::class)
    ->names('api.v1.articles');

Route::withoutMiddleware(ValidateJsonApiDocument::class)
    ->post('login', [LoginController::class, '__invoke'])
    ->name('api.v1.login');
