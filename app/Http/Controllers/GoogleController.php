<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    /**
     * Redirect user to Google login page
     */
    public function redirect(Request $request)
    {
        $mode = $request->query('mode', 'login');

        session(['google_mode' => $mode]);

        return Socialite::driver('google')->redirect();
    }


    /**
     * Handle Google callback
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $mode = session('google_mode', 'login');

            $existingUser = User::where(
                'email',
                $googleUser->getEmail()
            )->first();

            /**
             * LOGIN VALIDATION
             */
            if ($mode === 'login') {

                // account does not exist
                if (!$existingUser) {
                    return redirect(
                        env('FRONTEND_URL') .
                            '/login?error=Account not found. Please register first.'
                    );
                }

                // optional:
                // prevent login if not google account
                if (!$existingUser->google_id) {
                    return redirect(
                        env('FRONTEND_URL') .
                            '/login?error=This account was registered using email/password.'
                    );
                }

                $token = $existingUser
                    ->createToken('google-login')
                    ->plainTextToken;

                return redirect(
                    env('FRONTEND_URL') .
                        "/auth/google/callback?token={$token}"
                );
            }

            /**
             * REGISTER VALIDATION
             */
            if ($mode === 'register') {

                // email already exists
                if ($existingUser) {
                    return redirect(
                        env('FRONTEND_URL') .
                            '/register?error=Account already exists.'
                    );
                }

                // create new user
                $user = User::create([
                    'first_name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);

                $token = $user
                    ->createToken('google-register')
                    ->plainTextToken;

                return redirect(
                    env('FRONTEND_URL') .
                        "/auth/google/callback?token={$token}"
                );
            }

            return redirect(
                env('FRONTEND_URL') .
                    '/login?error=Invalid authentication mode.'
            );
        } catch (\Exception $e) {

            return redirect(
                env('FRONTEND_URL') .
                    '/login?error=Google authentication failed.'
            );
        }
    }
}
