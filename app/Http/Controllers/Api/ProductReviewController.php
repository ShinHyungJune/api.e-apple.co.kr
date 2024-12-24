<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductReviewRequest;
use App\Http\Resources\AvailableProductReviewResource;
use App\Http\Resources\ProductReviewResource;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
        $items = ProductReview::with(['user', 'product', 'productOption'])->search($filters)->latest()->paginate($request->take ?? 10);
        return ProductReviewResource::collection($items);
    }

    /**
     * 내 리뷰 목록
     * @priority 1
     * @responseFile storage/responses/product_reviews.json
     */
    public function myProductReviews(Request $request)
    {
        $items = ProductReview::mine()->with(['product', 'orderProduct'])->latest()->paginate($request->get('take', 10));
        return ProductReviewResource::collection($items);
    }

    /**
     * 나의 작성 가능한 리뷰 목록
     * @priority 1
     * @responseFile storage/responses/available_product_reviews.json
     */
    public function myAvailableProductReviews(Request $request)
    {
        $items = auth()->user()->availableProductReviews()->with(['product', 'productOption'])->oldest()->paginate($request->get('take', 10));
        return AvailableProductReviewResource::collection($items);
    }

    /**
     * 생성
     * @priority 1
     * @responseFile storage/responses/product_review.json
     */
    public function store(ProductReviewRequest $request)
    {
        $data = $request->validated();
        $availableProductReview = auth()->user()->availableProductReviews()->findOrFail($data['order_product_id']);
        $productReview = $availableProductReview->review()->create($data);
        //$productReview = auth()->user()->productReviews()->create($data);
        if ($request->file(ProductReview::IMAGES)) {
            foreach ($request->file(ProductReview::IMAGES) as $file) {
                $productReview->addMedia($file['file'])->toMediaCollection(ProductReview::IMAGES);
            }
        }

        auth()->user()->depositPoint($productReview);

        return $this->respondSuccessfully(ProductReviewResource::make($productReview));
    }


    /**
     * 상세
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/product_review.json
     */
    public function show(Request $request, ProductReview $productReview)
    {
        return $this->respondSuccessfully(ProductReviewResource::make($productReview));
    }


    /**
     * 수정
     * @priority 1
     * @responseFile storage/responses/product_review.json
     */
    public function update(ProductReviewRequest $request, $id)
    {
        $data = $request->validated();

        $productReview = ProductReview::mine()->findOrFail($id);

        if (!empty($data['files_remove_ids'])) {
            Media::where([['pointable_type', get_class($productReview)], ['pointable_id', $productReview->id]])
                ->whereIn('id', $data['files_remove_ids'])->delete();
        }

        if ($request->file(ProductReview::IMAGES)) {
            foreach ($request->file(ProductReview::IMAGES) as $file) {
                $productReview->addMedia($file['file'])->toMediaCollection(ProductReview::IMAGES);
            }
        }

        $productReview->update($data);
        return $this->respondSuccessfully(ProductReviewResource::make($productReview));
    }

    /**
     * 삭제
     * @priority 1
     */
    public function destroy(Request $request, $id)
    {
        DB::transaction(function () use ($id) {
            $productReview = ProductReview::mine()->findOrFail($id);
            $productReview->clearMediaCollection(ProductReview::IMAGES);
            /**
             * 포인트 차감
             * TODO 차감할 포인트가 없으면?
             */
            auth()->user()->withdrawalPoint($productReview);
            $productReview->delete($id);
        });

        return $this->respondSuccessfully();
    }
}
