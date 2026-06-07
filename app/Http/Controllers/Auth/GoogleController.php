<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')
            ->stateless()
            ->user();

        $user = User::where(
            'email',
            $googleUser->email
        )->first();

        if (!$user)
        {
            $user = User::create([

                'name' => $googleUser->name,

                'email' => $googleUser->email,

                'google_id' => $googleUser->id,

                'avatar' => $googleUser->avatar,

                'role' => 'user',

                'email_verified_at' => now(),

                'password' => bcrypt(
                    Str::random(24)
                ),

            ]);
        }
        else
        {
            $user->update([

                'google_id' => $googleUser->id,

                'avatar' => $googleUser->avatar,

            ]);
        }

        Auth::login(
            $user,
            true
        );

        return redirect('/')
            ->with(
                'success',
                'Welcome to Aanaya ✨'
            );
    }
}