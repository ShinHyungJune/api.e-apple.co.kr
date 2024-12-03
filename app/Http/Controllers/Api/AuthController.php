<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\BannerResource;
use App\Http\Resources\UserResource;
use App\Models\User;

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
     * @responseFile storage/responses/user.json
     */
    public function profile()
    {
        //return response()->json(auth()->user());
        return $this->respondSuccessfully(UserResource::make(auth()->user()));
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
            'expires_in' => auth()->factory()->getTTL() * 60
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
        $user = tap(new User($request->validated()))->save();
        return $this->respondSuccessfully(UserResource::make($user));
    }


}
