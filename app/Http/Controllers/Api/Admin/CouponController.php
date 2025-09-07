<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\CouponTypeMoment;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\CouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends ApiController
{
    public function init()
    {
        $typeMomentItems = CouponTypeMoment::getItems();
        return response()->json(compact('typeMomentItems'));
    }

    public function index(Request $request)
    {
        $filters = (array)json_decode($request->input('search'));
        $items = Coupon::withCount(['users'])->search($filters)->latest()->paginate($request->itemsPerPage ?? 30);
        return CouponResource::collection($items);
    }

    public function store(CouponRequest $request)
    {
        $data = $request->validated();
        $coupon = tap(new Coupon($data))->save();
        return $this->respondSuccessfully(new CouponResource($coupon));
    }

    public function show(Request $request, Coupon $coupon)
    {
        $coupon->loadCount(['users']);
        return $this->respondSuccessfully(new CouponResource($coupon));
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $coupon->loadCount(['users']);
        if ($coupon->users_count > 0) {
            abort(403, '다운로드된 쿠폰은 수정할 수 없습니다.');
        }
        $coupon->update($request->validated());
        return $this->respondSuccessfully(new CouponResource($coupon));
    }

    public function destroy(Request $request, Coupon $coupon)
    {
        $coupon->loadCount(['users']);
        if ($coupon->users_count > 0) {
            abort(403, '다운로드된 쿠폰은 수정할 수 없습니다.');
        }
        $coupon->delete();
        return $this->respondSuccessfully();
    }
}
