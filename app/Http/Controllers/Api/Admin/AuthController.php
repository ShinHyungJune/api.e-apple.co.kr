<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AuthController extends ApiController
{
    public function profile()
    {
        $user = User::findOrFail(auth()->id());
        return $this->respondSuccessfully(ProfileResource::make($user));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            //'username' => ['required', 'string', 'max:255', 'unique:users,username,' . auth()->id()],
            'email' => ['nullable', 'string', 'email', 'unique:users,email,' . auth()->id()],
            'name' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed',
                //Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()
                Password::min(8)->letters()->numbers()->symbols()
            ]
        ]);

        $user = tap(auth()->user())->update($data);
        return $this->respondSuccessfully(UserResource::make($user));
    }

}
