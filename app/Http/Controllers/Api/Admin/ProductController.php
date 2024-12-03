<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @group 관리자
 */
class ProductController extends ApiController
{
    /** 목록
     * @subgroup Product(상품)
     * @priority 1
     * @responseFile storage/responses/products.json
     */
    public function index()
    {
        $items = Product::query()->latest()->paginate(30);
        return ProductResource::collection($items);
    }

    /** 상세
     * @subgroup Product(상품)
     * @priority 1
     * @responseFile storage/responses/product.json
     */
    public function show(Product $product)
    {
        return $this->respondSuccessfully(ProductResource::make($product));
    }

    /**
     * 생성
     * @subgroup Product(상품)
     * @priority 1
     * @responseFile storage/responses/product.json
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $product = tap(new Product($data))->save();

        if ($request->file(Product::IMAGES)) {
            foreach ($request->file(Product::IMAGES) as $file) {
                $product->addMedia($file)->toMediaCollection('product_images');
            }
        }
        if ($request->file(Product::DESC_IMAGES)) {
            foreach ($request->file(Product::DESC_IMAGES) as $file) {
                $product->addMedia($file)->toMediaCollection(Product::DESC_IMAGES);
            }
        }

        return $this->respondSuccessfully(ProductResource::make($product));
    }

    /** 수정
     * @subgroup Product(상품)
     * @priority 1
     * @responseFile storage/responses/product.json
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validated();
        $product = ($product)->update($data);

        //TODO media 파일삭제는???
        if ($request->file(Product::IMAGES)) {
            foreach ($request->file(Product::IMAGES) as $file) {
                $product->addMedia($file)->toMediaCollection('product_images');
            }
        }
        if ($request->file(Product::DESC_IMAGES)) {
            foreach ($request->file(Product::DESC_IMAGES) as $file) {
                $product->addMedia($file)->toMediaCollection(Product::DESC_IMAGES);
            }
        }

        return $this->respondSuccessfully(ProductResource::make($product));
    }

    /** 삭제
     * @subgroup Product(상품)
     * @priority 1
     */
    public function destroy(Product $product)
    {
        //TODO media 파일삭제는???
        $product->delete();
        return $this->respondSuccessfully();
    }

}
