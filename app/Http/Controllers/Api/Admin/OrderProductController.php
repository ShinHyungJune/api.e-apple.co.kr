<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\DeliveryCompany;
use App\Enums\OrderStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\OrderProductRequest;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderProductController extends ApiController
{
    public function init()
    {
        $deliveryCompanyItems = DeliveryCompany::getItems();
        return response()->json(compact('deliveryCompanyItems'));
    }

    public function index(Request $request)
    {
        $filters = $request->input('search');
        $items = OrderProduct::with(['order', 'product', 'productOption'])
            ->whereIn('status', OrderStatus::CAN_DELVERY_MANAGES)
            ->search($filters)
            ->latest()->paginate($request->get('itemsPerPage', 10));
        return OrderProductResource::collection($items);
    }

    public function update(OrderProductRequest $request, $id = null)
    {
        $data = $request->validated();
        if ($id > 0) {
            OrderProduct::where('status', OrderStatus::DELIVERY_PREPARING)->findOrFail($id)->update($data);
        }

        if (!empty($data['ids']) && count($data['ids']) > 0) {
            $ids = $data['ids'];
            unset($data['ids']);
            OrderProduct::where('status', OrderStatus::DELIVERY_PREPARING)
                ->whereIn('id', $ids)
                ->update($data);
        }

        return $this->respondSuccessfully();
    }

}
