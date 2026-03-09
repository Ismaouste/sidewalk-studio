<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminOnboardingService
{
    public function __construct(
        protected SiteSettingsService $siteSettings,
    ) {}

    public function needsOnboarding(): bool
    {
        if (! Schema::hasTable('users')) {
            return true;
        }

        return User::query()->doesntExist();
    }

    /**
     * @param  array{name: string, email: string, password: string}  $payload
     */
    public function complete(array $payload): User
    {
        return DB::transaction(function () use ($payload): User {
            $this->siteSettings->bootstrap();

            $user = User::query()->create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'password' => Hash::make($payload['password']),
            ]);

            $this->siteSettings->completeOnboarding($user->email);

            return $user;
        });
    }
}
