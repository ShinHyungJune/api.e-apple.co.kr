<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    protected $additionalFields;

    public function __construct($resource, $additionalFields = [])
    {
        parent::__construct($resource);
        $this->additionalFields = $additionalFields;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request, ): array
    {
        //return parent::toArray($request);
        return $this->collection->map(function ($item) use ($request) {
            return (new UserResource($item, false, $this->additionalFields))->toArray($request);
        })->all();
    }


}
