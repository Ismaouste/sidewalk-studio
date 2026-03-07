<?php

namespace Tests\Feature;

use App\Models\AdminAuditLog;
use App\Models\User;
use App\Services\SiteSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_audit_log(): void
    {
        $this->get('/admin/audit-log')
            ->assertRedirect('/admin/login');
    }

    public function test_authenticated_operator_can_view_admin_audit_log(): void
    {
        $user = User::factory()->create();
        AdminAuditLog::query()->create([
            'actor_type' => 'console',
            'action' => 'operator.created',
            'subject' => 'operator_account',
            'summary' => [
                'created_user_email' => 'operator@example.test',
            ],
        ]);

        $this->actingAs($user)
            ->get('/admin/audit-log')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/AuditLog/Index')
                ->has('entries', 1)
                ->where('entries.0.action', 'operator.created')
                ->where('entries.0.summary.created_user_email', 'operator@example.test'));
    }

    public function test_successful_site_settings_update_creates_audit_entry(): void
    {
        $user = User::factory()->create();
        $payload = $this->settingsPayload([
            'site_identity' => [
                'name' => 'Audited Studio',
            ],
            'feature_toggles' => [
                'show_writing' => false,
            ],
        ]);

        $this->actingAs($user)
            ->put('/admin/settings', $payload)
            ->assertRedirect('/admin/settings');

        $entry = AdminAuditLog::query()->latest('created_at')->first();

        $this->assertNotNull($entry);
        $this->assertSame('site_settings.updated', $entry->action);
        $this->assertSame('site_settings', $entry->subject);
        $this->assertSame($user->id, $entry->actor_id);
        $this->assertSame(['feature_toggles', 'site_identity'], $entry->summary['changed_groups']);
        $this->assertSame(2, $entry->summary['field_count']);
        $this->assertSame([
            'feature_toggles.show_writing',
            'site_identity.name',
        ], $entry->summary['changed_fields']);
    }

    public function test_invalid_site_settings_update_does_not_create_audit_entry(): void
    {
        $user = User::factory()->create();
        $payload = $this->settingsPayload([
            'contact_details' => [
                'email' => 'not-an-email',
            ],
        ]);

        $this->actingAs($user)
            ->from('/admin/settings')
            ->put('/admin/settings', $payload)
            ->assertRedirect('/admin/settings')
            ->assertSessionHasErrors('contact_details.email');

        $this->assertDatabaseCount('admin_audit_logs', 0);
    }

    protected function settingsPayload(array $overrides = []): array
    {
        $defaults = app(SiteSettingsService::class)->defaults()->toPersistenceArray();

        return array_replace_recursive($defaults, $overrides);
    }
}
