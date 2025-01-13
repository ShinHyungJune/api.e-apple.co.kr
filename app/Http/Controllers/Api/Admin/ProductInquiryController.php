<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ProductInquiryRequest;
use App\Http\Resources\ProductInquiryResource;
use App\Models\ProductInquiry;
use Illuminate\Http\Request;

class ProductInquiryController extends ApiController
{
    public function index(Request $request)
    {
        $filters = (array)json_decode($request->input('search'));
        $items = ProductInquiry::with(['product', 'user'])->search($filters)->latest()->paginate($request->itemsPerPage ?? 30);
        return ProductInquiryResource::collection($items);
    }

    public function store(Request $request)
    {
    }

    public function show(Request $request, ProductInquiry $productInquiry)
    {
        $productInquiry->load(['product', 'user']);
        return $this->respondSuccessfully(new ProductInquiryResource($productInquiry));
    }

    public function update(ProductInquiryRequest $request, ProductInquiry $productInquiry)
    {
        $productInquiry->update($request->validated());
        return $this->respondSuccessfully(new ProductInquiryResource($productInquiry));
    }

    public function destroy(Request $request, ProductInquiry $productInquiry)
    {
        $productInquiry->delete();
        return $this->respondSuccessfully();
    }

}
