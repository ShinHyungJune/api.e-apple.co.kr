<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ProductPackageType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ProductPackageRequest;
use App\Http\Resources\ProductPackageResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductPackage;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductPackageController extends ApiController
{

    public function init(Request $request)
    {
        $typeItems = ProductPackageType::getItems();
        $productItems = ProductResource::collection(Product::all());
        return $this->respondSuccessfully(compact(['typeItems', 'productItems']));
    }

    public function index(Request $request)
    {
        $filters = (array)json_decode($request->input('search'));
        $items = ProductPackage::with(['products'])->search($filters)->latest()->paginate($request->itemsPerPage ?? 30);
        return ProductPackageResource::collection($items);
    }

    public function store(ProductPackageRequest $request)
    {
        $data = $request->validated();
        $productPackage = tap(new ProductPackage($data))->save();
        if ($request->file(ProductPackage::IMAGES)) {
            foreach ($request->file(ProductPackage::IMAGES) as $file) {
                $productPackage->addMedia($file)->toMediaCollection(ProductPackage::IMAGES);
            }
        }
        $productPackage->products()->sync($data['products']);
        return $this->respondSuccessfully(new ProductPackageResource($productPackage));
    }

    public function show(Request $request, ProductPackage $productPackage)
    {
        $productPackage->load(['products']);
        return $this->respondSuccessfully(new ProductPackageResource($productPackage));
    }

    public function update(ProductPackageRequest $request, ProductPackage $productPackage)
    {
        $data = $request->validated();
        $productPackage->update($data);
        if ($request->file(ProductPackage::IMAGES)) {
            foreach ($request->file(ProductPackage::IMAGES) as $file) {
                $productPackage->addMedia($file)->toMediaCollection(ProductPackage::IMAGES);
            }
        }
        $productPackage->products()->sync($data['products']);
        return $this->respondSuccessfully(new ProductPackageResource($productPackage));
    }

    public function destroy(Request $request, ProductPackage $productPackage)
    {
        $productPackage->products()->detach();
        $productPackage->delete();
        $productPackage->clearMediaCollection(ProductPackage::IMAGES);
        return $this->respondSuccessfully();
    }

    public function destroyImage(Media $media)
    {
        $media->delete();
        return $this->respondSuccessfully();
    }
}
