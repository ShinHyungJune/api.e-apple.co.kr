<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AvailableProductReviewResource;
use App\Http\Resources\ProductReviewResource;
use App\Models\ProductReview;
use Illuminate\Http\Request;

/**
 * @deprecated
 */
class MyController extends ApiController
{
    public function product_reviews(Request $request)
    {
        $items = ProductReview::mine()->with(['product', 'productOption'])->latest()->paginate($request->get('take', 10));
        return ProductReviewResource::collection($items);
    }

    public function available_product_reviews(Request $request)
    {
        $items = auth()->user()->availableProductReviews()->with(['product', 'productOption'])->oldest()->paginate($request->get('take', 10));
        return AvailableProductReviewResource::collection($items);
    }
}
