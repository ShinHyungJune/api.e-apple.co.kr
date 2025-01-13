<?php

namespace App\Http\Resources;

use App\Enums\ProductPackageType;
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
        $additionalFields = ($request->user()?->is_admin) ?
            [
                'type' => $this->type,
                'type_label' => ProductPackageType::from($this->type->value)->label(),
                'created_at' => $this->created_at,
                'products' => $this->products->pluck('id')->toArray(),
            ] :
            [
                'products' => ProductResource::collection($this->products),
            ];
        $return = [
            ...$additionalFields,
            ...$this->only(['id', 'title', 'description', 'category_title']),
            'img' => $this->getMedia(ProductPackage::IMAGES) ? MediaResource::make($this->getMedia(ProductPackage::IMAGES)[0] ?? null) : null,
            'imgs' => $this->getMedia(ProductPackage::IMAGES) ? MediaResource::collection($this->getMedia(ProductPackage::IMAGES)) : null,
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
