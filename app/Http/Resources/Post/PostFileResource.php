<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @deprecated
 */
class PostFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        $return = $this->only('id', 'file_name', 'original_url', 'mime_type', 'preview_url');
        //*
        if (config('scribe.response_file')) {
            $comments = [
                'id' => '기본키',
                'file_name' => '파일이름',
                'original_url' => '파일 URL',
                'mime_type' => '파일 형식',
                'preview_url' => '썸네일 URL'
            ];
            return getScribeResponseFile($return, 'media', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
