<?php

namespace App\Http\Resources;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        $additionalFields = ($request->user()?->is_admin) ? [
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
        ] : [];
        $return = [
            ...$additionalFields,
            ...$this->only(['id', 'title', 'description', 'url']),
            'img' => $this->getMedia(Banner::IMAGES) ? MediaResource::make($this->getMedia(Banner::IMAGES)[0] ?? null) : null,
            'imgs' => $this->getMedia(Banner::IMAGES) ? MediaResource::collection($this->getMedia(Banner::IMAGES) ?? null) : null,
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [];
            return getScribeResponseFile($return, 'banners', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
