<?php

namespace App\Providers;

use App\Audience\AudienceSink;
use App\Audience\LogAudienceSink;
use App\Audience\PostHogAudienceSink;
use App\Newsletter\BrevoNewsletterDriver;
use App\Newsletter\LogNewsletterDriver;
use App\Newsletter\NewsletterDriver;
use App\Services\SiteSettingsService;
use App\Support\Ssr\TimedHttpGateway;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\Ssr\HttpGateway;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SiteSettingsService::class);

        // The server render answers within seconds or the page goes out as it always did (see TimedHttpGateway).
        $this->app->singleton(HttpGateway::class, TimedHttpGateway::class);

        $this->app->bind(AudienceSink::class, fn (): AudienceSink => match (config('audience.sink')) {
            'posthog' => new PostHogAudienceSink,
            default => new LogAudienceSink,
        });

        $this->app->bind(NewsletterDriver::class, fn (): NewsletterDriver => match (config('newsletter.driver')) {
            'brevo' => new BrevoNewsletterDriver,
            default => new LogNewsletterDriver,
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
