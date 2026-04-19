<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class SocialAuthController extends Controller
{
    private const SUPPORTED_PROVIDERS = ['google'];

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::SUPPORTED_PROVIDERS, true), 404);

        if (! $this->providerIsConfigured($provider)) {
            return back()->withErrors([
                'social' => ucfirst($provider) . ' login is not configured yet. Add its credentials in your environment file first.',
            ]);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::SUPPORTED_PROVIDERS, true), 404);

        if (! $this->providerIsConfigured($provider)) {
            return redirect()->route('login')->withErrors([
                'social' => ucfirst($provider) . ' login is not configured yet.',
            ]);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (InvalidStateException) {
            return redirect()->route('login')->withErrors([
                'social' => 'The social login session expired. Please try again.',
            ]);
        } catch (Throwable $exception) {
            Log::warning('Social login failed.', [
                'provider' => $provider,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'social' => 'Unable to sign in with ' . ucfirst($provider) . ' right now. Please try again.',
            ]);
        }

        $providerId = (string) $socialUser->getId();
        $email = $socialUser->getEmail();

        if ($providerId === '') {
            return redirect()->route('login')->withErrors([
                'social' => ucfirst($provider) . ' did not return a valid account identifier.',
            ]);
        }

        if (! $email) {
            return redirect()->route('login')->withErrors([
                'social' => ucfirst($provider) . ' did not return an email address. Enable email access for the app and try again.',
            ]);
        }

        $user = DB::transaction(function () use ($provider, $providerId, $socialUser, $email) {
            $account = SocialAccount::query()
                ->with('user')
                ->where('provider_name', $provider)
                ->where('provider_id', $providerId)
                ->first();

            if ($account) {
                $this->updateAccountProfile($account, $socialUser);

                return $account->user;
            }

            $user = User::query()->where('email', $email)->first();

            if (! $user) {
                return null;
            }

            $this->linkAccount($user, $provider, $providerId, $socialUser);

            if (blank($user->name) && $socialUser->getName()) {
                $user->forceFill(['name' => $socialUser->getName()])->save();
            }

            return $user;
        });

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'social' => 'No payroll account matches ' . $email . '. Ask an administrator to create your user first.',
            ]);
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors([
                'social' => 'Your account is inactive. Please contact an administrator.',
            ]);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('status', ucfirst($provider) . ' account connected successfully.');
    }

    private function providerIsConfigured(string $provider): bool
    {
        $config = config('services.' . $provider, []);

        return filled($config['client_id'] ?? null)
            && filled($config['client_secret'] ?? null)
            && filled($config['redirect'] ?? null);
    }

    private function linkAccount(User $user, string $provider, string $providerId, mixed $socialUser): void
    {
        SocialAccount::query()->updateOrCreate(
            [
                'provider_name' => $provider,
                'provider_id' => $providerId,
            ],
            [
                'user_id' => $user->id,
                'provider_email' => $socialUser->getEmail(),
                'provider_nickname' => $socialUser->getNickname(),
                'avatar' => $socialUser->getAvatar(),
                'token' => $socialUser->token ?? null,
                'refresh_token' => $socialUser->refreshToken ?? null,
                'token_expires_at' => isset($socialUser->expiresIn) ? now()->addSeconds((int) $socialUser->expiresIn) : null,
            ]
        );
    }

    private function updateAccountProfile(SocialAccount $account, mixed $socialUser): void
    {
        $account->forceFill([
            'provider_email' => $socialUser->getEmail(),
            'provider_nickname' => $socialUser->getNickname(),
            'avatar' => $socialUser->getAvatar(),
            'token' => $socialUser->token ?? null,
            'refresh_token' => $socialUser->refreshToken ?? null,
            'token_expires_at' => isset($socialUser->expiresIn) ? now()->addSeconds((int) $socialUser->expiresIn) : null,
        ])->save();
    }
}
