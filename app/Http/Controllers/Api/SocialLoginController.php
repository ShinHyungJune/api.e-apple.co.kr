<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialLoginController extends ApiController
{
    public function login($service)
    {
        /*//SCOPE 필요없는 것은 삭제
        $scopes = [];
        $socialite = Socialite::driver($service)->scopes($scopes);
        $socialite->with(["access_type" => "offline", "prompt" => "consent select_account"]); //refreshToken을 함께 받으려면
        return $socialite->redirect();*/

        return Socialite::driver($service)->redirect();
    }

    public function callback(Request $request, $service)
    {
        //dd(Auth::user());

        /**
         * 세션 세팅을 해야 오류 발생하지 않음
         * .env => SESSION_DOMAIN=localhost
         */
        //$user = Socialite::driver('google')->user();

        /**
         * 상태를 유지하지 않는 인증
         * stateless 메소드는 세션의 상태를 확인하지 않게 하도록 하기 위해 사용될 수 있습니다.
         * 이는 소셜 로그인을 세션을 기반으로한 쿠키를 사용하지 않는 상태를 유지하지 않는 API에 추가할 때 유용합니다.
         */
        $socialite = Socialite::driver($service)->stateless()->user();
        //dd($socialite);

        $user = User::updateOrCreate(
            [
                //'email' => $socialite->email
                'social_id' => $socialite->id, 'social_platform' => $service,
            ],
            [
                'username' => $socialite->id . '|' . $service,
                'name' => $socialite->name ?? $socialite->nickname,
                'email' => $socialite?->email ?? null,
                'password' => $socialite->token, 'remember_token' => null,
                'social_id' => $socialite->id, 'social_platform' => $service,
                'nickname' => $socialite->nickname,
                //'last_login_at' => now()
            ]
        );
        //dd($user);
        /*if ($user->created_at === $user->updated_at) {
            MemberRegistered::dispatch($user);
        }*/

        /*$user = Auth::login($user); //$user = Auth::loginUsingId($user->id);
        $request->session()->regenerate();
        return redirect(env('FRONTEND_URL'));*/

        /*$token = $user->createToken('access-token')->plainTextToken;
        //$token = base64_encode($socialite->email . '|' . $socialite->token);
        return redirect(env('SOCIAL_REDIRECT_URL') . '/auth/callback?token=' . $token);*/


        /*$token = $user->createToken('access-token')->plainTextToken;
        return $this->respondWithToken($token);*/
        $token = JWTAuth::fromUser($user);
        return redirect(env('FRONTEND_URL') . '/auth/callback?token=' . $token);
    }

}
