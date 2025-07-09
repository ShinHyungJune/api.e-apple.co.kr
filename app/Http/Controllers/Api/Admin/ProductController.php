<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ProductCategory;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Code;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @group 관리자
 */
class ProductController extends ApiController
{

    public function init()
    {
        $categoryItems = ProductCategory::getItems();
        $productCategoryItems = Code::getItems(Code::PRODUCT_CATEGORY_ID);
        return $this->respondSuccessfully(compact(['categoryItems', 'productCategoryItems']));
    }

    /** 목록
     * @subgroup Product(상품)
     * @priority 1
     * @responseFile storage/responses/products.json
     */
    public function index(Request $request)
    {
        $filters = (array)json_decode($request->input('search'));
        $items = Product::with(['media', 'options'])->search($filters)->latest()->paginate($request->itemsPerPage ?? 30);
        return ProductResource::collection($items);
    }

    /** 상세
     * @subgroup Product(상품)
     * @priority 1
     * @responseFile storage/responses/product.json
     */
    public function show(Product $product)
    {
        $product->load('options');
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
        $product->options()->createMany($data['options']);

        if ($request->file(Product::IMAGES)) {
            foreach ($request->file(Product::IMAGES) as $file) {
                $product->addMedia($file)->toMediaCollection(Product::IMAGES);
            }
        }
        /*if ($request->file(Product::DESC_IMAGES)) {
            foreach ($request->file(Product::DESC_IMAGES) as $file) {
                $product->addMedia($file)->toMediaCollection(Product::DESC_IMAGES);
            }
        }*/

        return $this->respondSuccessfully(ProductResource::make($product));
    }

    /** 수정
     * @subgroup Product(상품)
     * @priority 1
     * @responseFile storage/responses/product.json
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $product->update($data);
        $options = $data['options'];
        //$product->options()->createMany($data['options']);
        $options = array_map(function ($item, $index) use ($product) {
            $item['id'] = $item['id'] ?? null;
            $item['product_id'] = $product->id;
            return $item;
        }, $options, array_keys($options));
        ProductOption::upsert($options, ['id'], ['product_id', 'name', 'price', 'original_price', 'stock_quantity']);

        if ($request->file(Product::IMAGES)) {
            foreach ($request->file(Product::IMAGES) as $file) {
                $product->addMedia($file)->toMediaCollection(Product::IMAGES);
            }
        }
        /*if ($request->file(Product::DESC_IMAGES)) {
            foreach ($request->file(Product::DESC_IMAGES) as $file) {
                $product->addMedia($file)->toMediaCollection(Product::DESC_IMAGES);
            }
        }*/

        return $this->respondSuccessfully(ProductResource::make($product));
    }

    /** 삭제
     * @subgroup Product(상품)
     * @priority 1
     */
    public function destroy(Product $product)
    {
        if ($product->orderProducts()->exists()) {
            abort(500, '주문된 상품이여서 삭제할 수 없습니다.');
        }
        $product->delete();
        $product->clearMediaCollection(Product::IMAGES);
        return $this->respondSuccessfully();
    }

    public function destroyImage(Media $media)
    {
        $media->delete();
        return $this->respondSuccessfully();
    }

    public function destroyOption(ProductOption $productOption)
    {
        $productOption->delete();
        return $this->respondSuccessfully();
    }

    public function storeImages(Request $request)
    {
        // 파일 검증
        //$request->validate(['upload' => 'required|file|mimes:jpeg,png,jpg,gif|max:5120',]);
        $request->validate(['upload' => 'required|file|mimes:jpeg,png,jpg,gif']);

        // 파일 저장
        $path = $request->file('upload')->store('products', 'public');

        // CKEditor에 반환할 URL
        $url = asset(Storage::url($path));

        return response()->json(['uploaded' => true, 'url' => $url,]);
    }


}
