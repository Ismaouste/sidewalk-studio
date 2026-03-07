<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCreateUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_create_user_command_creates_an_operator(): void
    {
        $this->artisan('admin:create-user', [
            'email' => 'operator@example.test',
            '--name' => 'Operator',
            '--password' => 'secret-password',
        ])
            ->expectsOutput('Created operator [operator@example.test].')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'operator@example.test',
            'name' => 'Operator',
        ]);

        $this->assertDatabaseHas('admin_audit_logs', [
            'actor_type' => 'console',
            'action' => 'operator.created',
            'subject' => 'operator_account',
        ]);
    }
}
