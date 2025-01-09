<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderProductController extends ApiController
{
    public function index(Request $request)
    {
        $items = OrderProduct::with(['product','productOption'])
            ->whereIn('status', OrderStatus::CAN_DELVERY_MANAGES)
            ->latest()->paginate($request->get('itemsPerPage', 10));
        return OrderProductResource::collection($items);
    }
}
