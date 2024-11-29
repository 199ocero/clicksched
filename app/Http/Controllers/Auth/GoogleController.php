<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the Google callback after authentication.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                if (! $user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }

                Auth::login($user);

                return to_route('dashboard')->with('flash_success', 'You are now logged in!');
            } else {
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                ]);

                Auth::login($newUser);

                return to_route('dashboard')->with('flash_success', 'You are now logged in!');
            }
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: '.$e->getMessage());

            return to_route('login')->with('flash_error', 'Unable to authenticate with Google');
        }
    }
}
