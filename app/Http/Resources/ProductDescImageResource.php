<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'file_name' => $this->file_name,
            'original_url' => $this->original_url,
            'preview_url' => $this->preview_url,
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [
                'id' => '기본키',
                'file_name' => '파일이름',
                'original_url' => '이미지 URL',
                'preview_url' => '썸네일 URL'
            ];
            return getScribeResponseFile($return, 'media', $comments);
        }
        //*/
        return $return;
        //*/

    }
}
