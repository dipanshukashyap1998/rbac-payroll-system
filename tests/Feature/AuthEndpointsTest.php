<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_and_register_pages(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }

    public function test_user_can_register_and_redirect_to_login(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Jane Admin',
            'email' => 'jane-admin@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'Registration successful. Please log in to continue.');

        $this->assertDatabaseHas('users', [
            'email' => 'jane-admin@example.com',
            'name' => 'Jane Admin',
        ]);
    }

    public function test_login_with_valid_credentials_redirects_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_works_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'logout@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_admin_without_company_is_redirected_to_company_creation(): void
    {
        $user = User::factory()->create([
            'email' => 'admin-nocompany@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $adminRole = $this->createRole('admin');
        $this->assignRoleToUser($user, $adminRole);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertRedirect(route('companies.create'));
    }
}
