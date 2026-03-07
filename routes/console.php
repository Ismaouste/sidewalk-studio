<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
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

    $user = User::query()->create([
        'name' => $name,
        'email' => $email,
        'password' => $password,
    ]);

    $this->info("Created operator [{$user->email}].");

    return Command::SUCCESS;
})->purpose('Create the first local operator account for the admin shell');
