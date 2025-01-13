<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCouponCollection;
use App\Models\UserCoupon;
use Illuminate\Http\Request;

class UserCouponController extends Controller
{
    public function index(Request $request)
    {
        $filters = (array)json_decode($request->input('search'));
        $items = UserCoupon::with(['coupon', 'user'])->search($filters)->latest()->paginate($request->itemsPerPage ?? 30);
        return new UserCouponCollection($items);
    }
}
