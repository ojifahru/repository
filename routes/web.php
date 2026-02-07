<?php

use App\Http\Controllers\OaiPmhController;
use App\Http\Controllers\Public\AuthorIndexController;
use App\Http\Controllers\Public\AuthorShowController;
use App\Http\Controllers\Public\CategoryIndexController;
use App\Http\Controllers\Public\CategoryShowController;
use App\Http\Controllers\Public\DocumentDownloadController;
use App\Http\Controllers\Public\DocumentFileController;
use App\Http\Controllers\Public\DocumentIndexController;
use App\Http\Controllers\Public\DocumentShowController;
use App\Http\Controllers\Public\DocumentTypeIndexController;
use App\Http\Controllers\Public\DocumentTypeShowController;
use App\Http\Controllers\Public\FacultyIndexController;
use App\Http\Controllers\Public\FacultyShowController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\StudyProgramIndexController;
use App\Http\Controllers\Public\StudyProgramShowController;
use App\Http\Controllers\Public\YearArchiveIndexController;
use App\Http\Controllers\Public\YearArchiveShowController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Models\Author;
use App\Models\TriDharma;
use Illuminate\Support\Facades\Route;

Route::match(['GET', 'POST'], '/oai', OaiPmhController::class)
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('oai');

Route::get('/', HomeController::class)->name('public.home');

Route::get('/robots.txt', RobotsController::class)->name('robots');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/dokumen', DocumentIndexController::class)->name('public.documents.index');

Route::get('/jenis-dokumen', DocumentTypeIndexController::class)->name('public.document-types.index');
Route::get('/jenis-dokumen/{documentType:slug}', DocumentTypeShowController::class)
    ->where('documentType', '[A-Za-z0-9-]+')
    ->name('public.document-types.show');

Route::get('/kategori', CategoryIndexController::class)->name('public.categories.index');
Route::get('/kategori/{category:slug}', CategoryShowController::class)
    ->where('category', '[A-Za-z0-9-]+')
    ->name('public.categories.show');

Route::get('/tahun', YearArchiveIndexController::class)->name('public.years.index');
Route::get('/tahun/{year}', YearArchiveShowController::class)
    ->where('year', '\\d{4}')
    ->name('public.years.show');

Route::get('/penulis', AuthorIndexController::class)->name('public.authors.index');
Route::get('/repository/{document:slug}', DocumentShowController::class)
    ->where('document', '[A-Za-z0-9-]+')
    ->name('public.repository.show');

Route::get('/repository/{document:slug}.pdf', DocumentFileController::class)
    ->where('document', '[A-Za-z0-9-]+')
    ->name('public.repository.pdf');

Route::get('/repository/{document:slug}/download', DocumentDownloadController::class)
    ->where('document', '[A-Za-z0-9-]+')
    ->name('public.repository.download');

Route::get('/dokumen/{id}', function (int $id) {
    $document = TriDharma::query()
        ->whereKey($id)
        ->where('status', 'published')
        ->firstOrFail();

    return redirect()->route('public.repository.show', $document, 301);
})->whereNumber('id')->name('public.documents.show');

Route::get('/dokumen/{id}/download', function (int $id) {
    $document = TriDharma::query()
        ->whereKey($id)
        ->where('status', 'published')
        ->firstOrFail();

    return redirect()->route('public.repository.download', $document, 301);
})->whereNumber('id')->name('public.documents.download');

Route::get('/penulis/{author:slug}', AuthorShowController::class)
    ->where('author', '[A-Za-z0-9-]+')
    ->name('public.authors.show');

Route::get('/fakultas', FacultyIndexController::class)->name('public.faculties.index');
Route::get('/fakultas/{faculty:slug}', FacultyShowController::class)
    ->where('faculty', '[A-Za-z0-9-]+')
    ->name('public.faculties.show');

Route::get('/program-studi', StudyProgramIndexController::class)->name('public.study-programs.index');
Route::get('/program-studi/{studyProgram:slug}', StudyProgramShowController::class)
    ->where('studyProgram', '[A-Za-z0-9-]+')
    ->name('public.study-programs.show');

Route::get('/author/{id}', function (int $id) {
    $author = Author::query()->whereKey($id)->firstOrFail();

    return redirect()->route('public.authors.show', $author, 301);
})->whereNumber('id')->name('public.authors.legacy');
