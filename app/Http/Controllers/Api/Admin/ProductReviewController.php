<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ProductReviewRequest;
use App\Http\Resources\ProductReviewResource;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductReviewController extends ApiController
{

    public function index(Request $request)
    {
        $filters = (array)json_decode($request->input('search'));
        $items = ProductReview::with(['product', 'user'])->search($filters)->latest()->paginate($request->itemsPerPage ?? 30);
        return ProductReviewResource::collection($items);
    }

    public function store(Request $request)
    {
    }

    public function show(Request $request, ProductReview $productReview)
    {
        $productReview->load(['product', 'user']);
        return $this->respondSuccessfully(new ProductReviewResource($productReview));
    }

    public function update(ProductReviewRequest $request, ProductReview $productReview)
    {
        $productReview->update($request->validated());
        if ($request->file(ProductReview::IMAGES)) {
            foreach ($request->file(ProductReview::IMAGES) as $file) {
                $productReview->addMedia($file)->toMediaCollection(ProductReview::IMAGES);
            }
        }
        return $this->respondSuccessfully(new ProductReviewResource($productReview));
    }

    public function destroy(Request $request, ProductReview $productReview)
    {
        $productReview->delete();
        $productReview->clearMediaCollection(ProductReview::IMAGES);
        return $this->respondSuccessfully();
    }

    public function destroyImage(Media $media)
    {
        $media->delete();
        return $this->respondSuccessfully();
    }
}
