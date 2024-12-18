<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\DeliveryAddressRequest;
use App\Http\Resources\DeliveryAddressResource;
use App\Models\DeliveryAddress;
use Illuminate\Http\Request;

/**
 * @group DeliveryAddress(배송지)
 */
class DeliveryAddressController extends ApiController
{

    /**
     * 목록
     * @priority 1
     * @responseFile storage/responses/delivery_addresses.json
     */
    public function index(Request $request)
    {
        $items = DeliveryAddress::mine()->latest()->paginate($request->take ?? 10);
        return DeliveryAddressResource::collection($items);
    }

    /**
     * 등록
     * @priority 1
     * @responseFile storage/responses/delivery_address.json
     */
    public function store(DeliveryAddressRequest $request)
    {
        $data = $request->validated();
        $item = tap(new DeliveryAddress($data))->save();
        return $this->respondSuccessfully(DeliveryAddressResource::make($item));
    }

    /**
     * 상세
     * @priority 1
     * @responseFile storage/responses/delivery_address.json
     */
    public function show(DeliveryAddress $deliveryAddress)
    {
        return $this->respondSuccessfully(DeliveryAddressResource::make($deliveryAddress));
    }

    /**
     * 수정
     * @priority 1
     * @responseFile storage/responses/delivery_address.json
     */
    public function update(DeliveryAddressRequest $request, DeliveryAddress $deliveryAddress)
    {
        $data = $request->validated();
        $item = tap($deliveryAddress)->update($data);
        return $this->respondSuccessfully(DeliveryAddressResource::make($item));
    }

    /**
     * 삭제
     * @priority 1
     */
    public function destroy(DeliveryAddress $deliveryAddress)
    {
        $deliveryAddress->delete();
        return $this->respondSuccessfully();
    }
}
