<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FindPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VerifyNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/**
 * @group 인증
 */
class AuthController extends ApiController
{

    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * 로그인
     * @priority 1
     * @unauthenticated
     */
    public function login(LoginRequest $request)
    {
        //$credentials = request(['email', 'password']);
        if(!$request->token)
            $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);

        if($request->token && auth()->user())
            return $this->respondWithToken($request->token);

        $credentials = request(['username', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            //return response()->json(['error' => '이메일 또는 비밀번호를 잘못 입력했습니다.'], 422);
            //abort(422, '이메일 또는 비밀번호를 잘못 입력했습니다.');
            abort(response()->json([
                'message' => '아이디 또는 비밀번호를 잘못 입력했습니다.',
                'errors' => ['username' => '아이디 또는 비밀번호를 잘못 입력했습니다.'],
            ], 422));
        }


        //관리자 확인
        if (str_contains(Route::current()->uri(), 'admin')) {
            if (!auth()->user()->is_admin) {
                abort(response()->json([
                    'message' => '아이디 또는 비밀번호를 잘못 입력했습니다.',
                    'errors' => ['email' => '아이디 또는 비밀번호를 잘못 입력했습니다.'],
                ], 422));
            }
        }


        return $this->respondWithToken($token);
    }

    /**
     * 회원정보
     * @priority 1
     * @responseFile storage/responses/profile.json
     */
    public function profile()
    {
        //return response()->json(auth()->user());
        /*$user = auth()->user()->withCount([
            'availableCoupons',//사용 가능한 쿠폰 개수
            'availableProductReviews',//작성 가능한 상품 리뷰 개수
            'productReviews', //내 상품 리뷰 개수
        ])->first();*/
        $user = User::withCount([
            'availableCoupons',//사용 가능한 쿠폰 개수
            'availableProductReviews',//작성 가능한 상품 리뷰 개수
            'productReviews', //내 상품 리뷰 개수
        ])->findOrFail(auth()->id());
        return $this->respondSuccessfully(ProfileResource::make($user));
    }

    /**
     * 로그아웃
     * @priority 1
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * 토큰 새로고침
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

    /**
     * 회원정보수정
     * @priority 1
     * @responseFile storage/responses/user.json
     */
    public function update(RegisterRequest $request)
    {
        $data = $request->validated();

        if (!empty($data['phone'])) {
            VerifyNumber::check($data['phone']);
        }

        $user = tap(auth()->user())->update($data);
        return $this->respondSuccessfully(UserResource::make($user));
    }

    /**
     * 회원 비밀번호 변경
     * @priority 1
     * @responseFile storage/responses/user.json
     */
    public function updatePassword(PasswordRequest $request)
    {
        $data = $request->validated();
        if (!Hash::check($data['current_password'], auth()->user()->password)) {
            //abort(422, '기존 비밀번호를 확인해 주세요.');
            abort(response()->json(['message' => '기존 비밀번호를 확인해 주세요.', 'errors' => ['password' => '기존 비밀번호를 확인해 주세요.']],
                422));
        }
        auth()->user()->update(['password' => Hash::make($data['password'])]);

        return $this->respondSuccessfully();
    }

    /**
     * 회원탈퇴
     * @priority 1
     */
    public function destroy(Request $request)
    {
        DB::transaction(function () use ($request) {

            //auth()->user()->orders()->delete();// 주문은 유지
            auth()->user()->carts()->delete();
            auth()->user()->coupons()->detach();// pivot 테이블 => user_coupons
            auth()->user()->pointTransactions()->delete();
            auth()->user()->deliveryAddresses()->delete();
            auth()->user()->inquiries()->delete();
            auth()->user()->productReviews()->delete();
            auth()->user()->productInquiries()->delete();
            auth()->user()->delete();

            $user = auth()->user();
            $user->delete();
            auth()->logout();
        });
        return $this->respondSuccessfully();
    }



    /**
     * 아이디찾기
     * @unauthenticated
     * @priority 1
     */
    public function findId(Request $request)
    {
        $request->validate(['phone' => ['required', 'digits_between:10,11']]);

        $user = User::where("phone", $request->phone)->first();
        if (!$user) {
            //abort(404, '해당 정보로 가입된 계정이 존재하지 않습니다.');
            abort(response()->json(['message' => '해당 정보로 가입된 계정이 존재하지 않습니다.',
                'errors' => ['user' => '해당 정보로 가입된 계정이 존재하지 않습니다.']],
                404));
        }

        $verifyNumber = VerifyNumber::where('ids', $request->phone)->where('verified', true)->first();
        if (!$verifyNumber) {
            //abort(422, '연락처를 인증해주세요.');
            abort(response()->json(['message' => '연락처를 인증해주세요.',
                'errors' => ['phone' => '연락처를 인증해주세요.']],
                422));
        }

        $verifyNumber->delete();
        return $this->respondSuccessfully(['email' => $user->email]);
    }

    /**
     * 비민번호찾기
     * @unauthenticated
     * @priority 1
     */
    public function findPassword(FindPasswordRequest $request)
    {
        $data = $request->validated();

        $user = User::where("phone", $data['phone'])->where("email", $data['email'])->first();
        if (!$user) {
            //abort(404, '가입할 때 입력했던 연락처와 아이디를 다시 확인해주세요.');
            abort(response()->json(['message' => '가입할 때 입력했던 연락처와 아이디를 다시 확인해주세요.',
                'errors' => ['user' => '가입할 때 입력했던 연락처와 아이디를 다시 확인해주세요.']],
                404));
        }

        $verifyNumber = VerifyNumber::where('ids', $data['phone'])->where('verified', true)->first();
        if (!$verifyNumber) {
            //abort(422, '연락처를 인증해주세요.');
            abort(response()->json(['message' => '연락처를 인증해주세요.',
                'errors' => ['phone' => '연락처를 인증해주세요.']],
                422));
        }
        //$verifyNumber->delete();

        return $this->respondSuccessfully(UserResource::make($user));
    }


    /**
     * 비민번호찾기(비밀번호 Reset)
     * @unauthenticated
     * @priority 1
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();

        $user = User::where("phone", $data['phone'])->where("email", $data['email'])->first();
        if (!$user) {
            //abort(404, '가입할 때 입력했던 연락처와 아이디를 다시 확인해주세요.');
            abort(response()->json(['message' => '가입할 때 입력했던 연락처와 아이디를 다시 확인해주세요.',
                'errors' => ['user' => '가입할 때 입력했던 연락처와 아이디를 다시 확인해주세요.']],
                404));
        }

        $verifyNumber = VerifyNumber::where('ids', $data['phone'])->where('verified', true)->first();
        if (!$verifyNumber) {
            //abort(422, '연락처를 인증해주세요.');
            abort(response()->json(['message' => '연락처를 인증해주세요.',
                'errors' => ['user' => '연락처를 인증해주세요.']],
                422));
        }
        $verifyNumber->delete();
        $user->update(['password' => Hash::make($data['password'])]);

        return $this->respondSuccessfully(UserResource::make($user));
    }


}
