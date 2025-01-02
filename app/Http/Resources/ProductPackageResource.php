<?php

namespace App\Http\Resources;

use App\Models\ProductPackage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        $return = [
            ...$this->only(['id', 'title', 'description', 'category_title']),
            'img' => $this->getMedia(ProductPackage::IMAGES) ? MediaResource::make($this->getMedia(ProductPackage::IMAGES)[0] ?? null) : null,
            'imgs' => $this->getMedia(ProductPackage::IMAGES) ? MediaResource::collection($this->getMedia(ProductPackage::IMAGES)) : null,
            'products' => ProductResource::collection($this->products),
        ];
        //*
        if (config('scribe.response_file')) {
            $comments = [];
            return getScribeResponseFile($return, 'product_packages', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
