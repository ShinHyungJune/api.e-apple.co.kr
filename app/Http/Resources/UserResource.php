<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    private $isMasking;

    // 생성자에서 여분의 데이터를 받음
    public function __construct($resource, $isMasking = false)
    {
        parent::__construct($resource);
        $this->isMasking = $isMasking;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        $return = $this->only('id', 'name', 'email', 'phone', 'nickname');
        if ($this->isMasking) {
            $id = explode('@', $this->email)[0];
            $email = substr($id, 0, 3) . str_repeat('*', max(strlen($id) - 3, 0));
            $return = ['email' => $email];
        }

        //*/
        if (config('scribe.response_file')) {
            return getScribeResponseFile($return, 'users');
        }
        //*/
        return $return;
        //*/
    }
}
