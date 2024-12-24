<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PointResource;
use App\Models\Point;
use Illuminate\Http\Request;

/**
 * @group Point(적립금)
 */
class PointController extends ApiController
{
    /**
     * 목록
     * @priority 1
     * @queryParam type 구분 Example: deposit, withdrawal, expiration
     * @responseFile storage/responses/points.json
     */
    public function index(Request $request)
    {
        //적립, 사용, 소멸
        $filters = $request->only(['type']);

        $items = Point::with('pointable')->mine()->search($filters)->latest()->paginate($request->get('take', 10));
        $items->map(function ($item) {
            return $item->append('order_id');
        });
        return PointResource::collection($items);
    }
}
