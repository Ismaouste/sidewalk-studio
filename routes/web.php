<?php

use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\SiteSettingsController as AdminSiteSettingsController;
use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WritingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::redirect('/about', '/experience', 301)->name('about');
Route::get('/experience', [SiteController::class, 'experience'])->name('experience');
Route::get('/local', [SiteController::class, 'local'])->name('local');
Route::get('/projects', [SiteController::class, 'projects'])->name('projects');
Route::get('/labs', [SiteController::class, 'labs'])->name('labs');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');

Route::get('/writing', [WritingController::class, 'index'])->name('writing.index');
Route::get('/writing/{slug}', [WritingController::class, 'show'])->name('writing.show');

Route::get('/case-studies', [CaseStudyController::class, 'index'])->name('case-studies.index');
Route::get('/case-studies/{slug}', [CaseStudyController::class, 'show'])->name('case-studies.show');

Route::get('/cv/{locale}', [SiteController::class, 'downloadCv'])
    ->whereIn('locale', ['en', 'fr'])
    ->name('career.cv.download');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('authenticate');

    Route::middleware('admin.auth')->group(function (): void {
        Route::redirect('/', '/admin/settings')->name('index');
        Route::get('/audit-log', [AdminAuditLogController::class, 'index'])->name('audit-log.index');
        Route::get('/settings', [AdminSiteSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [AdminSiteSettingsController::class, 'update'])->name('settings.update');
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', function () {
    $content = implode(PHP_EOL, [
        'User-agent: *',
        'Allow: /',
        'Sitemap: '.url('/sitemap.xml'),
    ]);

    return response($content, 200)->header('Content-Type', 'text/plain');
})->name('robots');
