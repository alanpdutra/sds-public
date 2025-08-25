<?php

use App\Http\Controllers\Web\AuthorPageController;
use App\Http\Controllers\Web\BookPageController;
use App\Http\Controllers\Web\DocumentationController;
use App\Http\Controllers\Web\ReportPageController;
use App\Http\Controllers\Web\SubjectPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/books'));

Route::get('/books', [BookPageController::class, 'index'])->name('books.index');
Route::get('/books/create', [BookPageController::class, 'create'])->name('books.create');
Route::get('/books/{id}/edit', [BookPageController::class, 'edit'])->name('books.edit');

Route::get('/authors', [AuthorPageController::class, 'index'])->name('authors.index');
Route::get('/authors/create', [AuthorPageController::class, 'create'])->name('authors.create');
Route::get('/authors/{id}/edit', [AuthorPageController::class, 'edit'])->name('authors.edit');

Route::get('/subjects', [SubjectPageController::class, 'index'])->name('subjects.index');
Route::get('/subjects/create', [SubjectPageController::class, 'create'])->name('subjects.create');
Route::get('/subjects/{id}/edit', [SubjectPageController::class, 'edit'])->name('subjects.edit');

Route::get('/reports', [ReportPageController::class, 'index'])->name('reports.index');
Route::get('/reports/pdf', [ReportPageController::class, 'pdf'])->name('reports.pdf');

Route::get('/doc', [DocumentationController::class, 'index'])->name('documentation.index');
