<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductInquiryRequest;
use App\Http\Resources\ProductInquiryResource;
use App\Models\ProductInquiry;
use Illuminate\Http\Request;

/**
 * @group Product Inquiry(상품 문의)
 */
class ProductInquiryController extends ApiController
{
    /**
     * 목록
     * @priority 1
     * @queryParam type Example: photo, text
     * @unauthenticated
     * @responseFile storage/responses/product_inquiries.json
     */
    public function index(Request $request)
    {
        $filters = $request->only(['type']);
        $items = ProductInquiry::query()->search($filters)->latest()->paginate($request->take ?? 30);
        return ProductInquiryResource::collection($items);
    }

    /**
     * 생성
     * @priority 1
     * @responseFile storage/responses/product_inquiry.json
     */
    public function store(ProductInquiryRequest $request)
    {
        $data = $request->validated();
        $productInquiry = auth()->user()->productInquiries()->create($data);//->load('product');
        return $this->respondSuccessfully(ProductInquiryResource::make($productInquiry));
    }


}
