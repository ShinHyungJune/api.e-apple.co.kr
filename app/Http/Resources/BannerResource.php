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
        $return = [
            ...$this->only(['title', 'description', 'url']),
            'img' => $this->getMedia(Banner::IMAGES) ? MediaResource::make($this->getMedia(Banner::IMAGES)[0] ?? null) : null,
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
