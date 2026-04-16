<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook', 'twitter'], true), 404);

        return back()->with('status', ucfirst($provider) . ' login is shown as UI option, but OAuth is not configured yet.');
    }
}
