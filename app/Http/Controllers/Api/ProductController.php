<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductCategory;
use App\Http\Resources\MdProductPackageResource;
use App\Http\Resources\ProductResource;
use App\Models\MdProductPackage;
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
     * @queryParam category_id 카테고리 Example: 1
     * @queryParam subcategory_id 하위 카테고리 Example: 2
     * @queryParam order_column 정렬컬럼 Example: price, reviews_count, created_at
     * @queryParam order_direction 정렬방법 Example: desc, asc
     * @queryParam tags string[] Example: ["실시간 인기","클래식 과일","어른을 위한 픽","추가 증정"]
     * @responseFile storage/responses/products.json
     */
    public function index(Request $request, $category = null)
    {
        $filters = $request->only(['min_price', 'max_price', 'tags', 'category_id', 'subcategory_id']);
        $orders = $request->only(['order_column', 'order_direction']);

        if ($category && ProductCategory::BEST->value === ProductCategory::from($category)->value) {
            $request->take = $request->take ?? 20;
        } else {
            $request->take = $request->take ?? 30;
        }

        //DB::enableQueryLog();
        $items = Product::query()
            //->withCount(['inquiries', 'reviews'])
            ->with(['media', 'options', 'inquiries', 'reviews'])
            ->category($category)
            ->search($filters)
            ->sortBy($orders)
            ->paginate($request->take);
        //Log::info(DB::getQueryLog());

        return ProductResource::collection($items);
    }

    /**
     * MD 추천 선물
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/md_product_packages.json
     */
    public function mdPackages()
    {
        $items = MdProductPackage::with(['products'])->has('products')->latest()->get();
        return MdProductPackageResource::collection($items);
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
        $product->load(['reviews','inquiries']);
        return $this->respondSuccessfully(ProductResource::make($product));
    }

}
