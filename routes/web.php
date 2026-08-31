<?php

use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminBrandingController;
use App\Http\Controllers\Admin\AdminContactSubmissionController;
use App\Http\Controllers\Admin\AdminEntryController;
use App\Http\Controllers\Admin\AdminLanguageFileController;
use App\Http\Controllers\Admin\AdminLoaderQuoteController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminPublicationController;
use App\Http\Controllers\Admin\AdminThemeController;
use App\Http\Controllers\Admin\Auth\AdminOnboardingController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\SiteSettingsController as AdminSiteSettingsController;
use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\ContactSubmissionController;
use App\Http\Controllers\ContentVisualController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WritingController;
use App\Support\PublicLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return redirect()->to(
        PublicLocale::localizedPath('/', PublicLocale::preferredLocaleForRequest($request)),
        302,
    );
});

Route::prefix('{locale}')
    ->whereIn('locale', PublicLocale::supported())
    ->group(function (): void {
        Route::get('/', [SiteController::class, 'home'])->name('home');
        Route::get('/experience', [SiteController::class, 'experience'])->name('experience');
        Route::get('/madeof', function (string $locale) {
            return redirect("/{$locale}/sparkle", 302);
        });
        Route::get('/sparkle', [SiteController::class, 'sparkle'])->name('sparkle');
        Route::get('/local', [SiteController::class, 'local'])->name('local');
        Route::get('/projects', [SiteController::class, 'projects'])->name('projects');
        Route::get('/labs', [SiteController::class, 'labs'])->name('labs');
        Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
        Route::post('/contact', [ContactSubmissionController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('contact.store');
        Route::get('/data-processing', [SiteController::class, 'dataProcessing'])->name('data-processing');
        Route::get('/colophon', [SiteController::class, 'colophon'])->name('colophon');

        Route::get('/writing', function (string $locale) {
            return redirect("/{$locale}/journal", 301);
        });
        Route::get('/writing/{slug}', function (string $locale, string $slug) {
            return redirect("/{$locale}/journal/{$slug}", 301);
        })->name('writing.legacy.show');
        Route::get('/journal', [WritingController::class, 'index'])->name('writing.index');
        Route::get('/journal/{slug}', [WritingController::class, 'show'])->name('writing.show');

        Route::get('/case-studies', [CaseStudyController::class, 'index'])->name('case-studies.index');
        Route::get('/case-studies/{slug}', [CaseStudyController::class, 'show'])->name('case-studies.show');
    });

Route::get('/about', function (Request $request) {
    return redirect()->to(
        PublicLocale::localizedPath('/projects', PublicLocale::preferredLocaleForRequest($request)),
        301,
    );
});

foreach ([
    '/local',
    '/projects',
    '/labs',
    '/contact',
    '/data-processing',
    '/journal',
    '/case-studies',
] as $legacyPath) {
    Route::get($legacyPath, function (Request $request) use ($legacyPath) {
        return redirect()->to(
            PublicLocale::localizedPath($legacyPath, PublicLocale::preferredLocaleForRequest($request)),
            301,
        );
    });
}

Route::get('/madeof', function (Request $request) {
    return redirect()->to(
        PublicLocale::localizedPath('/sparkle', PublicLocale::preferredLocaleForRequest($request)),
        302,
    );
});

Route::get('/sparkle', function (Request $request) {
    return redirect()->to(
        PublicLocale::localizedPath('/sparkle', PublicLocale::preferredLocaleForRequest($request)),
        302,
    );
});

Route::get('/experience', function (Request $request) {
    return redirect()->to(
        PublicLocale::localizedPath('/projects', PublicLocale::preferredLocaleForRequest($request)),
        301,
    );
});

Route::get('/writing', function (Request $request) {
    return redirect()->to(
        PublicLocale::localizedPath('/journal', PublicLocale::preferredLocaleForRequest($request)),
        301,
    );
});

Route::get('/writing/{slug}', function (Request $request, string $slug) {
    return redirect()->to(
        PublicLocale::localizedPath("/journal/{$slug}", PublicLocale::preferredLocaleForRequest($request)),
        301,
    );
});

Route::get('/journal/{slug}', function (Request $request, string $slug) {
    return redirect()->to(
        PublicLocale::localizedPath("/journal/{$slug}", PublicLocale::preferredLocaleForRequest($request)),
        301,
    );
})->where('slug', '.*');

Route::get('/case-studies/{slug}', function (Request $request, string $slug) {
    return redirect()->to(
        PublicLocale::localizedPath("/case-studies/{$slug}", PublicLocale::preferredLocaleForRequest($request)),
        301,
    );
})->where('slug', '.*');

Route::get('/content-visuals/{section}/{slug}.svg', ContentVisualController::class)->name('content-visuals.show');

Route::get('/cv/{locale}', [SiteController::class, 'downloadCv'])
    ->whereIn('locale', ['en', 'fr'])
    ->name('career.cv.download');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', AdminEntryController::class)->name('index');
    Route::get('/onboarding', [AdminOnboardingController::class, 'create'])->name('onboarding.create');
    Route::post('/onboarding', [AdminOnboardingController::class, 'store'])->name('onboarding.store');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('authenticate');

    Route::middleware('admin.auth')->group(function (): void {
        Route::get('/contact-submissions', [AdminContactSubmissionController::class, 'index'])->name('contact-submissions.index');
        Route::get('/audit-log', [AdminAuditLogController::class, 'index'])->name('audit-log.index');
        Route::get('/settings', [AdminSiteSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [AdminSiteSettingsController::class, 'update'])->name('settings.update');
        Route::get('/theme', [AdminThemeController::class, 'edit'])->name('theme.edit');
        Route::put('/theme', [AdminThemeController::class, 'update'])->name('theme.update');
        Route::post('/theme/rebuild', [AdminThemeController::class, 'rebuild'])->name('theme.rebuild');
        Route::get('/branding', [AdminBrandingController::class, 'edit'])->name('branding.edit');
        Route::post('/branding', [AdminBrandingController::class, 'update'])->name('branding.update');
        Route::get('/loader-quotes', [AdminLoaderQuoteController::class, 'index'])->name('loader-quotes.index');
        Route::put('/loader-quotes', [AdminLoaderQuoteController::class, 'update'])->name('loader-quotes.update');
        Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
        Route::get('/pages/{page}/{locale}', [AdminPageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}/{locale}', [AdminPageController::class, 'update'])->name('pages.update');
        Route::get('/publications', [AdminPublicationController::class, 'index'])->name('publications.index');
        Route::get('/publications/create/{type}', [AdminPublicationController::class, 'create'])->name('publications.create');
        Route::post('/publications', [AdminPublicationController::class, 'store'])->name('publications.store');
        Route::get('/publications/{type}/{locale}/{slug}', [AdminPublicationController::class, 'edit'])->name('publications.edit');
        Route::put('/publications/{type}/{locale}/{slug}', [AdminPublicationController::class, 'update'])->name('publications.update');
        Route::put('/publication-type-settings', [AdminPublicationController::class, 'updateTypeSettings'])->name('publications.type-settings.update');
        Route::get('/language-files', [AdminLanguageFileController::class, 'index'])->name('language-files.index');
        Route::put('/language-files/{key}', [AdminLanguageFileController::class, 'update'])->name('language-files.update');
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
