<?php

use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WritingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/projects', [SiteController::class, 'projects'])->name('projects');
Route::get('/labs', [SiteController::class, 'labs'])->name('labs');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');

Route::get('/writing', [WritingController::class, 'index'])->name('writing.index');
Route::get('/writing/{slug}', [WritingController::class, 'show'])->name('writing.show');

Route::get('/case-studies', [CaseStudyController::class, 'index'])->name('case-studies.index');
Route::get('/case-studies/{slug}', [CaseStudyController::class, 'show'])->name('case-studies.show');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', function () {
    $content = implode(PHP_EOL, [
        'User-agent: *',
        'Allow: /',
        'Sitemap: '.url('/sitemap.xml'),
    ]);

    return response($content, 200)->header('Content-Type', 'text/plain');
})->name('robots');
