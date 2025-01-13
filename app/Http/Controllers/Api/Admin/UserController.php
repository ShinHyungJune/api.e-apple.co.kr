<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    public function index(Request $request)
    {
        $filters = $request->input('search');
        $items = User::member()->search($filters)->latest()->paginate($request->itemsPerPage ?? 30);
        return new UserCollection($items, ['points', 'level', 'created_at']);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $user = tap(new User($data))->save();
        return $this->respondSuccessfully(new UserResource($user, false, ['points', 'level', 'created_at']));
    }

    public function show(User $user)
    {
        return $this->respondSuccessfully(new UserResource($user, false, ['points', 'level', 'created_at']));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        $user = tap($user)->update($data);
        return $this->respondSuccessfully(new UserResource($user, false, ['points', 'level', 'created_at']));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return $this->respondSuccessfully();
    }

}
