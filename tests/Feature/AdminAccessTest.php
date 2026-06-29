<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Panel;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    public function test_guest_is_redirected_to_admin_login_from_admin_root(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_non_admin_user_cannot_access_admin_panel(): void
    {
        $user = new class extends User {
            public function canAccessPanel(Panel $panel): bool
            {
                return $panel->getId() !== 'admin';
            }
        };

        $user->forceFill([
            'id' => 999999,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'status' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    public function test_admin_user_can_access_admin_panel(): void
    {
        $user = new class extends User {
            public function canAccessPanel(Panel $panel): bool
            {
                return true;
            }
        };

        $user->forceFill([
            'id' => 1000000,
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'status' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin/login');

        $response->assertRedirect('/admin');
    }
}