<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = trim((string) env('ADMIN_SEED_EMAIL', ''));
        $password = (string) env('ADMIN_SEED_PASSWORD', '');

        if ($email === '' || $password === '') {
            return;
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => trim((string) env('ADMIN_SEED_NAME', 'Sidewalk Admin')),
                'password' => $password,
            ],
        );
    }
}
