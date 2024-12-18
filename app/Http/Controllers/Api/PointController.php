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
     * @responseFile storage/responses/points.json
     */
    public function index(Request $request)
    {
        $items = Point::mine()->latest()->paginate($request->get('take', 10));
        return PointResource::collection($items);
    }
}
