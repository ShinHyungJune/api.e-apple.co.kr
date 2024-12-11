<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardCategoryResource extends JsonResource
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
            'value' => $this->id,
            'text' => $this->name,
        ];
        //*
        if (config('scribe.response_file')) {
            $comments = ['value' => '기본키', 'text' => '카테고리 이름'];
            return getScribeResponseFile($return, 'board_categories', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
