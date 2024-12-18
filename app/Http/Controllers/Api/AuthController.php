<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\BannerResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VerifyNumber;

class AuthController extends ApiController
{

    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * 로그인
     * @group 인증
     * @priority 1
     * @unauthenticated
     */
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * 회원정보
     * @group 인증
     * @priority 1
     * @responseFile storage/responses/profile.json
     */
    public function profile()
    {
        //return response()->json(auth()->user());
        $user = auth()->user()->withCount('availableCoupons')->first();
        return $this->respondSuccessfully(ProfileResource::make($user));
    }

    /**
     * 로그아웃
     * @group 인증
     * @priority 1
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * 토큰 새로고침
     * @group 인증
     * @priority 1
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => UserResource::make(auth()->user())
        ]);
    }

    /**
     * 회원가입
     * @group 인증
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/user.json
     */
    public function store(RegisterRequest $request)
    {
        $data = $request->validated();

        VerifyNumber::check($data['phone']);

        $user = tap(new User($data))->save();
        return $this->respondSuccessfully(UserResource::make($user));
    }


}
