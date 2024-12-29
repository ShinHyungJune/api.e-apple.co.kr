<?php

namespace App\Http\Resources;

use App\Models\Banner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SweetnessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        Carbon::setLocale('ko');
        $return = [
            ...$this->only(['fruit_name', 'sweetness', 'standard_sweetness']),
            'standard_datetime' => Carbon::parse($this->standard_datetime)->translatedFormat('d일(D) H시'),
            'img' => $this->getMedia(Banner::IMAGES) ? MediaResource::make($this->getMedia(Banner::IMAGES)[0] ?? null) : null,
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [];
            return getScribeResponseFile($return, 'sweetnesses', $comments);
        }
        //*/
        return $return;
        //*/

    }
}
