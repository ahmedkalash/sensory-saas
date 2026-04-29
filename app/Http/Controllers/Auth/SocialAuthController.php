<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            /** @var \Laravel\Socialite\Two\User $socialUser */
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('app/login')->with('error', 'Authentication failed.');
        }

        if (! $socialUser->getEmail()) {
            return redirect('app/login')->with('error', 'An email address is required to log in.');
        }

        // Try to find user by social ID
        $user = User::where('provider_name', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (! $user) {
            // Try to find user by email
            $user = User::where('email', $socialUser->getEmail())->first();

            if (! $user) {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'type' => UserType::Customer,
                ]);
            }

            // Link account
            $user->update([
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        // Update tokens
        $user->update([
            'social_token' => $socialUser->token,
            'social_refresh_token' => $socialUser->refreshToken,
            'social_expires_at' => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
        ]);

        Auth::login($user);

        return redirect('/app');
    }
}
