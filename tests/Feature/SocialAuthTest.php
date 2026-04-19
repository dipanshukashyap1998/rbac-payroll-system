<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_redirects_to_google_when_provider_is_configured(): void
    {
        config()->set('services.google', [
            'client_id' => 'google-client-id',
            'client_secret' => 'google-client-secret',
            'redirect' => 'http://localhost/auth/google/callback',
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $this->get(route('social.login', 'google'))
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_it_links_a_social_account_to_an_existing_user_by_email(): void
    {
        config()->set('services.google', [
            'client_id' => 'google-client-id',
            'client_secret' => 'google-client-secret',
            'redirect' => 'http://localhost/auth/google/callback',
        ]);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'name' => 'Existing Admin',
        ]);

        $provider = Mockery::mock(Provider::class);
        $socialUser = Mockery::mock(SocialiteUser::class);

        $socialUser->shouldReceive('getId')->once()->andReturn('google-123');
        $socialUser->shouldReceive('getEmail')->times(2)->andReturn('admin@example.com');
        $socialUser->shouldReceive('getNickname')->once()->andReturn('rbac-admin');
        $socialUser->shouldReceive('getAvatar')->once()->andReturn('https://example.com/avatar.jpg');

        $socialUser->token = 'access-token';
        $socialUser->refreshToken = 'refresh-token';
        $socialUser->expiresIn = 3600;

        $provider->shouldReceive('user')->once()->andReturn($socialUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $response = $this->get(route('social.callback', 'google'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'provider_name' => 'google',
            'provider_id' => 'google-123',
            'provider_email' => 'admin@example.com',
        ]);
    }

    public function test_it_rejects_social_login_when_no_matching_user_exists(): void
    {
        config()->set('services.google', [
            'client_id' => 'google-client-id',
            'client_secret' => 'google-client-secret',
            'redirect' => 'http://localhost/auth/google/callback',
        ]);

        $provider = Mockery::mock(Provider::class);
        $socialUser = Mockery::mock(SocialiteUser::class);

        $socialUser->shouldReceive('getId')->once()->andReturn('google-404');
        $socialUser->shouldReceive('getEmail')->once()->andReturn('missing@example.com');

        $provider->shouldReceive('user')->once()->andReturn($socialUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $response = $this->get(route('social.callback', 'google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('social');
        $this->assertGuest();
        $this->assertDatabaseCount('social_accounts', 0);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
