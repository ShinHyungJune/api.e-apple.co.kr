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
     * @queryParam is_answered Example: 1, 0
     * @unauthenticated
     * @responseFile storage/responses/product_inquiries.json
     */
    public function index(Request $request)
    {
        $filters = $request->only(['is_answered']);
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

    /**
     * 나의 상품문의 전체 목록
     * @priority 1
     * @queryParam is_answered Example: 1, 0
     * @unauthenticated
     * @responseFile storage/responses/product_inquiries.json
     */
    public function mine(Request $request)
    {
        $filters = $request->only(['is_answered']);
        $items = ProductInquiry::with('product')->mine()->search($filters)->latest()->paginate($request->take ?? 30);
        return ProductInquiryResource::collection($items);
    }

}
