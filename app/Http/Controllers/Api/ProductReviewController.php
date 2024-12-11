<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductReviewRequest;
use App\Http\Resources\ProductReviewResource;
use App\Models\ProductReview;
use Illuminate\Http\Request;

/**
 * @group Product Review(상품 후기)
 */
class ProductReviewController extends ApiController
{

    /**
     * 목록
     * @priority 1
     * @queryParam type Example: photo, text
     * @queryParam take Example: 10
     * @unauthenticated
     * @responseFile storage/responses/product_reviews.json
     */
    public function index(Request $request)
    {
        $filters = $request->only(['type']);

        $items = ProductReview::query()->search($filters)->latest()->paginate($request->take ?? 10);
        return ProductReviewResource::collection($items);
    }


    /**
     * 생성
     * @priority 1
     * @responseFile storage/responses/product_review.json
     */
    public function store(ProductReviewRequest $request)
    {
        $data = $request->validated();

        $productReview = auth()->user()->productReviews()->create($data);
        if ($request->file(ProductReview::IMAGES)) {
            foreach ($request->file(ProductReview::IMAGES) as $file) {
                $productReview->addMedia($file)->toMediaCollection(ProductReview::IMAGES);
            }
        }

        return $this->respondSuccessfully(ProductReviewResource::make($productReview));
    }

}
