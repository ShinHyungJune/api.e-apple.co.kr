<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;

class OrderController extends ApiController
{
    /**
     * 테스트용 임시
     */
    public function test()
    {
        $user = User::findOrFail(2);
        $user->update(['email'=>'test@test.com', 'password' => '123456']);
        return $this->respondSuccessfully($user);
    }
}
