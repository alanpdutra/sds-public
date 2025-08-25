<?php

use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SubjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::apiResource('books', BookController::class);

    Route::apiResource('authors', AuthorController::class);
    Route::get('authors-options', [AuthorController::class, 'options']);

    Route::apiResource('subjects', SubjectController::class);
    Route::get('subjects-options', [SubjectController::class, 'options']);

    Route::get('reports/summary', [ReportController::class, 'summary']);
});
