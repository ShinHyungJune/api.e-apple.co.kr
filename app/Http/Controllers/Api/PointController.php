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

        $items = Point::mine()->search($filters)->latest()->paginate($request->get('take', 10));
        return PointResource::collection($items);
    }
}
