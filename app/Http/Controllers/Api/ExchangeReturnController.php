<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ExchangeReturnRequest;
use App\Http\Resources\ExchangeReturnResource;
use App\Models\ExchangeReturn;
use App\Models\Order;
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
    public function store(ExchangeReturnRequest $request, $id)
    {
        $order = Order::mine($request)->delivery()->findOrFail($id);

        $data = $request->validated();
        $item = $order->exchangeReturns()->create($data)->load('order');

        return $this->respondSuccessfully(ExchangeReturnResource::make($item));
    }

}
