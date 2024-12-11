<?php

namespace App\Http\Resources;

use App\Models\MdProductPackage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MdProductPackageResource extends JsonResource
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
            ...$this->only(['id', 'title', 'description']),
            'images' => $this->getMedia(MdProductPackage::IMAGES) ? MediaResource::collection($this->getMedia(MdProductPackage::IMAGES)) : null,
            'products' => ProductResource::collection($this->products),
        ];
        //*
        if (config('scribe.response_file')) {
            return getScribeResponseFile($return, 'md_product_packages');
        }
        //*/
        return $return;
        //*/
    }
}
