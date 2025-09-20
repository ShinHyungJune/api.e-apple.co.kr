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
            $data['issued_until'] = $this->formatIssuedUntil($data['issued_until']);
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
            $data['issued_until'] = $this->formatIssuedUntil($data['issued_until']);
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

    private function formatIssuedUntil($issuedUntil)
    {
        // datetime-local input 형식 처리
        if (strpos($issuedUntil, 'T') !== false) {
            // T가 있으면 datetime-local 형식 (YYYY-MM-DDTHH:mm)
            $dateStr = str_replace('T', ' ', $issuedUntil) . ':00';
        } else if (strlen($issuedUntil) === 19) {
            // 이미 Y-m-d H:i:s 형식
            $dateStr = $issuedUntil;
        } else if (strlen($issuedUntil) === 16) {
            // Y-m-d H:i 형식인 경우 초 추가
            $dateStr = $issuedUntil . ':00';
        } else {
            // 그 외의 경우 그대로 사용
            $dateStr = $issuedUntil;
        }

        // 날짜가 너무 미래인 경우 (2038년 이후) 처리
        try {
            $maxDate = new \DateTime('2038-01-01 00:00:00');
            $inputDate = new \DateTime($dateStr);

            if ($inputDate > $maxDate) {
                return $maxDate->format('Y-m-d H:i:s');
            } else {
                return $inputDate->format('Y-m-d H:i:s');
            }
        } catch (\Exception $e) {
            // 날짜 파싱 에러시 원본 반환
            return $issuedUntil;
        }
    }
}
