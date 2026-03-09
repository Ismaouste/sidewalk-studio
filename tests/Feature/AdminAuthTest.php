<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use App\Services\SiteSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'site.settings_source' => 'database',
            'site.admin_enabled' => true,
        ]);
    }

    public function test_admin_login_page_is_reachable(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/Auth/Login'));
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get('/admin/settings')
            ->assertRedirect('/admin/login');
    }

    public function test_operator_can_log_in_and_reach_admin_settings(): void
    {
        $user = User::factory()->create([
            'password' => 'secret-password',
        ]);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'secret-password',
            'remember' => true,
        ])
            ->assertRedirect('/admin/settings');

        $this->assertAuthenticatedAs($user);

        $this->get('/admin/settings')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/Settings/Edit')
                ->where('settings.site_identity.name', 'Ismael Rodmacq'));
    }

    public function test_invalid_admin_login_returns_errors(): void
    {
        User::factory()->create([
            'password' => 'secret-password',
        ]);

        $this->from('/admin/login')
            ->post('/admin/login', [
                'email' => 'wrong@example.test',
                'password' => 'nope',
            ])
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_operator_can_update_site_settings(): void
    {
        $user = User::factory()->create();
        $payload = $this->settingsPayload([
            'site_identity' => [
                'name' => 'Admin Updated Studio',
            ],
            'contact_details' => [
                'email' => 'admin@updated-studio.test',
            ],
            'feature_toggles' => [
                'show_labs' => false,
            ],
            'theme_settings' => [
                'sunset_glow' => '#c88274',
            ],
        ]);

        $this->actingAs($user)
            ->put('/admin/settings', $payload)
            ->assertRedirect('/admin/settings')
            ->assertSessionHas('status', 'Site settings saved. Public reads now use the updated payload and the change was added to the audit log.');

        $record = SiteSetting::query()->findOrFail(SiteSetting::SINGLETON_ID);

        $this->assertSame('Admin Updated Studio', $record->site_identity['name']);
        $this->assertSame('admin@updated-studio.test', $record->contact_details['email']);
        $this->assertFalse($record->feature_toggles['show_labs']);
        $this->assertSame('#c88274', $record->theme_settings['sunset_glow']);
    }

    public function test_authenticated_operator_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest();
    }

    public function test_invalid_site_settings_update_returns_validation_errors_without_persisting(): void
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

        $this->assertDatabaseCount('site_settings', 0);
    }

    protected function settingsPayload(array $overrides = []): array
    {
        $defaults = app(SiteSettingsService::class)->defaults()->toPersistenceArray();

        return array_replace_recursive($defaults, $overrides);
    }
}
