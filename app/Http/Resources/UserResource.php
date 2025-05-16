<?php

namespace App\Http\Resources;

use App\Enums\UserLevel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $isMasking;
    protected $additionalFields;

    // 생성자에서 여분의 데이터를 받음
    public function __construct($resource, $isMasking = false, $additionalFields = [])
    {
        parent::__construct($resource);
        $this->isMasking = $isMasking;
        $this->additionalFields = $additionalFields;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        $return = $this->only(['id',
            'name', 'email', 'phone', 'nickname', 'is_agree_promotion',
            'username', 'postal_code', 'address', 'address_detail',
            ...$this->additionalFields
        ]);
        if ($this->isMasking) {
            $id = explode('@', $this->email)[0];
            $email = substr($id, 0, 3) . str_repeat('*', max(strlen($id) - 3, 0));
            $return = ['email' => $email];
        }
        if (!empty($return['level'])) {
            $return['level'] = UserLevel::from($this->level->value)->label();
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
