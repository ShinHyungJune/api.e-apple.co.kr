<?php

namespace App\Http\Resources;

use App\Models\ProductPackage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPackageCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        $groupedProducts = $this->resource->groupBy('category_id')->map(function ($group) {
            return [
                'package_category_id' => $group->first()->category_id,
                //'total_count' => $group->count(),
                'packages' => $group->map(function ($item) {
                    return [
                        ...$item->only(['id', 'title', 'description']),
                        'img' => $item->getMedia(ProductPackage::IMAGES) ? MediaResource::make($item->getMedia(ProductPackage::IMAGES)[0] ?? null) : null,
                        'imgs' => $item->getMedia(ProductPackage::IMAGES) ? MediaResource::collection($item->getMedia(ProductPackage::IMAGES)) : null,
                        'products' => ProductResource::collection($item->products),
                    ];
                })
            ];
        });
        return $groupedProducts->toArray();
    }
}
