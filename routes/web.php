<?php

use App\Http\Controllers\Public\AuthorShowController;
use App\Http\Controllers\Public\DocumentDownloadController;
use App\Http\Controllers\Public\DocumentIndexController;
use App\Http\Controllers\Public\DocumentShowController;
use App\Http\Controllers\Public\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('public.home');

Route::get('/dokumen', DocumentIndexController::class)->name('public.documents.index');
Route::get('/dokumen/{id}', DocumentShowController::class)->whereNumber('id')->name('public.documents.show');
Route::get('/dokumen/{id}/download', DocumentDownloadController::class)->whereNumber('id')->name('public.documents.download');

Route::get('/author/{id}', AuthorShowController::class)->whereNumber('id')->name('public.authors.show');
