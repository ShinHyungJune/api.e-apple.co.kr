<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\DeliveryAddressRequest;
use App\Http\Resources\DeliveryAddressResource;
use App\Models\DeliveryAddress;
use Illuminate\Http\Request;

class DeliveryAddressController extends ApiController
{

    public function index(Request $request)
    {
        $filters = (array)json_decode($request->input('search'));
        $items = DeliveryAddress::with(['user'])->search($filters)->latest()->paginate($request->itemsPerPage ?? 30);
        return DeliveryAddressResource::collection($items);
    }

    public function store(DeliveryAddressRequest $request)
    {
        $data = $request->validated();
        $deliveryAddress = tap(new DeliveryAddress($data))->save();
        return $this->respondSuccessfully(new DeliveryAddressResource($deliveryAddress));
    }

    public function show(Request $request, DeliveryAddress $deliveryAddress)
    {
        $deliveryAddress->load(['user']);
        return $this->respondSuccessfully(new DeliveryAddressResource($deliveryAddress));
    }

    public function update(DeliveryAddressRequest $request, DeliveryAddress $deliveryAddress)
    {
        $deliveryAddress->update($request->validated());
        return $this->respondSuccessfully(new DeliveryAddressResource($deliveryAddress));
    }

    public function destroy(Request $request, DeliveryAddress $deliveryAddress)
    {
        $deliveryAddress->delete();
        return $this->respondSuccessfully();
    }

}
