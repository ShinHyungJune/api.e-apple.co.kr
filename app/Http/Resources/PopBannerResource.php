<?php

namespace App\Http\Resources;

use App\Models\PopBanner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopBannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $additionalFields = ($request->user()?->is_admin) ? [
            'is_active' => $this->is_active,
            'started_at' => $this->started_at?->format('Y-m-d H:i:s'),
            'finished_at' => $this->finished_at?->format('Y-m-d H:i:s'),
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ] : [];

        $return = [
            ...$this->only(['id', 'title', 'url']),
            'img' => $this->getFirstMedia(PopBanner::IMAGE) ? MediaResource::make($this->getFirstMedia(PopBanner::IMAGE)) : null,
            ...$additionalFields,
        ];

        if (config('scribe.response_file')) {
            $comments = [];
            return getScribeResponseFile($return, 'pop_banners', $comments);
        }

        return $return;
    }
}
