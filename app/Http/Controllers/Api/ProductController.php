<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductCategory;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @group Product(상품)
 */
class ProductController extends ApiController
{

    /**
     * 목록
     * @priority 1
     * @unauthenticated
     * @queryParam min_price 최소 가격 Example: 1000
     * @queryParam max_price 최대 가격 Example: 2000
     * @queryParam tags string[] Example: ["실시간 인기","클래식 과일","어른을 위한 픽","추가 증정"]
     * @responseFile storage/responses/products.json
     */
    public function index(Request $request, $category = null)
    {
        $filters = $request->only(['min_price', 'max_price', 'tags']);

        $perPage = 30;
        if ($category && ProductCategory::BEST->value === ProductCategory::from($category)->value) {
            $perPage = 20;
        }

        $query = Product::query()
            //->withCount(['reviews'])
            ->with(['reviews'])
            ->category($category)->search($filters)->latest();
        //Log::info($query->toSql(), $query->getBindings());
        $items = $query->simplePaginate($perPage);

        return ProductResource::collection($items);
    }

    /**
     * MD 추천 선물
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/products.json
     */
    public function mdSuggestionGifts()
    {
        $items = Product::where('is_md_suggestion_gift', true)->latest()->get();
        return ProductResource::collection($items);
    }

    /**
     * 상세
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/product.json
     */
    public function show(Product $product)
    {
        $product->increment('view_count');
        return $this->respondSuccessfully(ProductResource::make($product));
    }

}
