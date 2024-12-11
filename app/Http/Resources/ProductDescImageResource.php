<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @deprecated
 */
class ProductDescImageResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->file_name,
            'url' => $this->original_url,
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [
                'id' => '기본키',
                'name' => '파일이름',
                'url' => '이미지 URL',
            ];
            return getScribeResponseFile($return, 'media', $comments);
        }
        //*/
        return $return;
        //*/

    }
}
