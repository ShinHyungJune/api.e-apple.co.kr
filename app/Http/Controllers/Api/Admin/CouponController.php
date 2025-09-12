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
        
        // issued_until 날짜 형식 처리 및 유효성 검증
        if (isset($data['issued_until'])) {
            // T 제거 및 :00 초 추가
            $dateStr = str_replace('T', ' ', $data['issued_until']) . ':00';
            
            // 날짜가 너무 미래인 경우 (2038년 이후) 처리
            $maxDate = new \DateTime('2038-01-01 00:00:00');
            $inputDate = new \DateTime($dateStr);
            
            if ($inputDate > $maxDate) {
                $data['issued_until'] = $maxDate->format('Y-m-d H:i:s');
            } else {
                $data['issued_until'] = $dateStr;
            }
        }
        
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
        // 수정 권한 체크 제거 - 모든 쿠폰 수정 가능
        
        $data = $request->validated();
        
        // issued_until 날짜 형식 처리 및 유효성 검증
        if (isset($data['issued_until'])) {
            // T 제거 및 :00 초 추가
            $dateStr = str_replace('T', ' ', $data['issued_until']) . ':00';
            
            // 날짜가 너무 미래인 경우 (2038년 이후) 처리
            $maxDate = new \DateTime('2038-01-01 00:00:00');
            $inputDate = new \DateTime($dateStr);
            
            if ($inputDate > $maxDate) {
                $data['issued_until'] = $maxDate->format('Y-m-d H:i:s');
            } else {
                $data['issued_until'] = $dateStr;
            }
        }
        
        $coupon->update($data);
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
