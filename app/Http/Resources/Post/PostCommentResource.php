<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostCommentResource extends JsonResource
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
            ...$this->only(['content']),
            'user' => UserResource::make($this->user)
        ];
        //*
        if (config('scribe.response_file')) {
            return getScribeResponseFile($return, 'post_comments');
        }
        //*/
        return $return;
        //*/
    }
}
