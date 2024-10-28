<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Helper\JWTToken;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'firstName' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt(uniqid()), // You can generate a random password or leave it null if not needed.
                    'role' => 'user',
                ]);
            }

            $token = JWTToken::CreateToken($user->email, $user->id, $user->role);

            return redirect()->to('/user/dashboard')->cookie('token', $token, 60 * 24 * 30);

        } catch (\Exception $e) {
            return redirect()->route('login.page')->with('error', 'Something went wrong or you denied the request.');
        }
    }
}
