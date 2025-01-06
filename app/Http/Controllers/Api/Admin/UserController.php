<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserCollection;
use App\Models\User;

class UserController extends ApiController
{
    //
    public function index()
    {
        $items = User::member()->latest()->paginate();
        return new UserCollection($items, ['points', 'level', 'created_at']);
    }
}
