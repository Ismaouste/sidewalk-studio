<?php

use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminBrandingController;
use App\Http\Controllers\Admin\AdminContactSubmissionController;
use App\Http\Controllers\Admin\AdminEntryController;
use App\Http\Controllers\Admin\AdminExperienceEntryController;
use App\Http\Controllers\Admin\AdminLanguageFileController;
use App\Http\Controllers\Admin\AdminLoaderQuoteController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminPublicationController;
use App\Http\Controllers\Admin\AdminQuestionnaireController;
use App\Http\Controllers\Admin\AdminThemeController;
use App\Http\Controllers\Admin\Auth\AdminOnboardingController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\SiteSettingsController as AdminSiteSettingsController;
use App\Http\Controllers\AudiencePingController;
use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\ContactSubmissionController;
use App\Http\Controllers\ContentVisualController;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WritingController;
use App\Http\Middleware\CachePublicResponse;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\ResolvePublicLocale;
use App\Support\PublicLocale;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/', function (Request $request) {
    return redirect()->to(
        PublicLocale::localizedPath('/', PublicLocale::preferredLocaleForRequest($request)),
        302,
    );
});

/*
 * The T1 audience ping is deliberately stateless: no session, no CSRF
 * (there is no session to protect), no cookie in the response - the
 * absence of state is what makes the endpoint exemptable.
 */
Route::post('/audience', AudiencePingController::class)
    ->name('audience.ping')
    ->withoutMiddleware([
        StartSession::class,
        ShareErrorsFromSession::class,
        PreventRequestForgery::class,
        ResolvePublicLocale::class,
        HandleInertiaRequests::class,
        AddLinkHeadersForPreloadedAssets::class,
        CachePublicResponse::class,
    ]);

/*
 * Newsletter signup is stateless for the same reason /audience is: the
 * blocks render on cached public pages, so there is no per-visitor CSRF
 * token to rely on. Abuse is bounded by the throttle and the honeypot.
 */
Route::post('/newsletter', NewsletterSubscriptionController::class)
    ->name('newsletter.subscribe')
    ->middleware('throttle:6,1')
    ->withoutMiddleware([
        StartSession::class,
        ShareErrorsFromSession::class,
        PreventRequestForgery::class,
        ResolvePublicLocale::class,
        HandleInertiaRequests::class,
        AddLinkHeadersForPreloadedAssets::class,
        CachePublicResponse::class,
    ]);

Route::prefix('{locale}')
    ->whereIn('locale', PublicLocale::supported())
    ->group(function (): void {
        Route::get('/', [SiteController::class, 'home'])->name('home');
        /**
         * The record is read at `/experience`, which is what the menu, the
         * crumb and the page itself have always called it. It answered at
         * `/projects` until that name was the only place on the site still
         * saying the other word, so the two routes swapped: the page moved up
         * to the name, and the old address became the redirect.
         */
        Route::get('/experience', [SiteController::class, 'experience'])->name('experience');
        Route::get('/projects', [SiteController::class, 'projectsLegacy'])->name('projects.legacy');
        Route::get('/madeof', function (string $locale) {
            return redirect("/{$locale}/sparkle", 302);
        });
        Route::get('/sparkle', [SiteController::class, 'sparkle'])->name('sparkle');
        Route::get('/local', [SiteController::class, 'local'])->name('local');
        Route::get('/labs', [SiteController::class, 'labs'])->name('labs');
        Route::get('/services', [SiteController::class, 'services'])->name('services');
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
        PublicLocale::localizedPath('/experience', PublicLocale::preferredLocaleForRequest($request)),
        301,
    );
});

/**
 * Unprefixed paths redirect to the reader's locale. `/colophon` was the one
 * public page missing from this list, so `site.test/colophon` answered 404
 * while its seven siblings redirected — and the static export, which fetches
 * unprefixed paths, could not export it at all.
 */
foreach ([
    '/local',
    '/experience',
    '/labs',
    '/services',
    '/contact',
    '/data-processing',
    '/colophon',
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

Route::get('/projects', function (Request $request) {
    return redirect()->to(
        PublicLocale::localizedPath('/experience', PublicLocale::preferredLocaleForRequest($request)),
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
        Route::post('/pages/{page}/{locale}/revert', [AdminPageController::class, 'revert'])->name('pages.revert');
        Route::post('/pages/{page}/{locale}/preview', [AdminPageController::class, 'preview'])->name('pages.preview');
        Route::get('/experience', [AdminExperienceEntryController::class, 'index'])->name('experience.index');
        Route::get('/experience/create', [AdminExperienceEntryController::class, 'create'])->name('experience.create');
        Route::post('/experience', [AdminExperienceEntryController::class, 'store'])->name('experience.store');
        Route::get('/experience/{entry}', [AdminExperienceEntryController::class, 'edit'])->name('experience.edit');
        Route::put('/experience/{entry}', [AdminExperienceEntryController::class, 'update'])->name('experience.update');
        Route::delete('/experience/{entry}', [AdminExperienceEntryController::class, 'destroy'])->name('experience.destroy');
        Route::get('/questionnaire', [AdminQuestionnaireController::class, 'index'])->name('questionnaire.index');
        Route::put('/questionnaire', [AdminQuestionnaireController::class, 'update'])->name('questionnaire.update');
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
