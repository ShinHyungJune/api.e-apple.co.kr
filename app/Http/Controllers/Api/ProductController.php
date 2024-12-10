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
     * @queryParam tags string[] Example: ["실시간 인기","클래식 과일","어른을 위한 픽","추가 증정"]
     * @responseFile storage/responses/products.json
     */
    public function index(Request $request, $category = null)
    {
        $filters = $request->only(['min_price', 'max_price', 'tags']);

        if ($category && ProductCategory::BEST->value === ProductCategory::from($category)->value) {
            $request->take = $request->take ?? 20;
        } else {
            $request->take = $request->take ?? 30;
        }

        //DB::enableQueryLog();
        $query = Product::query()
            ->withCount(['inquiries'])
            ->with(['reviews'])
            ->category($category)->search($filters)->latest();
        $items = $query->paginate($request->take);
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
