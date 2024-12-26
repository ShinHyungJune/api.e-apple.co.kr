<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        $return = parent::toArray($request);
        //*
        if (config('scribe.response_file')) {
            $comments = ['value' => '기본키', 'text' => '코드명'];
            return getScribeResponseFile($return, 'codes', $comments);
        }
        //*/
        return $return;
        //*/

    }
}
