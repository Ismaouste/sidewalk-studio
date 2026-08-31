<?php

use App\Models\User;
use App\Services\AdminAuditLogService;
use App\Services\SiteSettingsService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create-user {email} {--name=} {--password=}', function (string $email): int {
    if (User::query()->where('email', $email)->exists()) {
        $this->error("A user with [{$email}] already exists.");

        return Command::FAILURE;
    }

    $name = $this->option('name') ?: text(
        label: 'Operator name',
        required: true,
    );

    $password = $this->option('password') ?: password(
        label: 'Operator password',
        required: true,
    );

    $user = DB::transaction(function () use ($email, $name, $password): User {
        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        app(AdminAuditLogService::class)->recordOperatorCreated($user);

        return $user;
    });

    $this->info("Created operator [{$user->email}].");

    return Command::SUCCESS;
})->purpose('Create the first local operator account for the admin shell');

Artisan::command('site:export-settings {--path=}', function (SiteSettingsService $siteSettings): int {
    $path = $this->option('path')
        ? base_path((string) $this->option('path'))
        : database_path('seeders/data/site-settings.json');

    File::ensureDirectoryExists(dirname($path));
    File::put(
        $path,
        json_encode(
            $siteSettings->current()->toPersistenceArray(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
        ).PHP_EOL,
    );

    $this->info("Exported site settings snapshot to [{$path}].");

    return Command::SUCCESS;
})->purpose('Export the current site settings row into the project seed snapshot');
