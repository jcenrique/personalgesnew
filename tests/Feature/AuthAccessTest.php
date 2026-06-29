<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthAccessTest extends TestCase
{
    public function test_guest_is_redirected_to_app_login_from_root(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('filament.app.auth.login'));
    }

    public function test_authenticated_user_is_redirected_away_from_app_login(): void
    {
        $user = User::factory()->make([
            'status' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/');
    }
}