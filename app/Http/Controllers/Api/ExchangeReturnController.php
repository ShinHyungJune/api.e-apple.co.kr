<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Requests\ExchangeReturnRequest;
use App\Http\Resources\ExchangeReturnResource;
use App\Models\ExchangeReturn;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

/**
 * @group ExchangeReturn(교환/반품)
 */
class ExchangeReturnController extends ApiController
{

    /**
     * 목록
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/exchange_returns.json
     */
    public function index(Request $request)
    {
        $items = ExchangeReturn::with(['order'])->mine()->latest()->paginate($request->get('take', 10));
        return ExchangeReturnResource::collection($items);
    }

    /**
     * 등록
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/exchange_return.json
     */
    public function store(ExchangeReturnRequest $request)
    {
        $data = $request->validated();

        $orderProduct = OrderProduct::mine($request)->possibleExchangeReturnStatus()->findOrFail($data['order_product_id']);
        $item = $orderProduct->exchangeReturns()->create($data);
        $orderProduct->update(['status' => OrderStatus::EXCHANGE_REQUESTED]);

        return $this->respondSuccessfully(ExchangeReturnResource::make($item));
    }

}
