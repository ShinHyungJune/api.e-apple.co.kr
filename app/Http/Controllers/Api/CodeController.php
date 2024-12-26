<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CodeResource;
use App\Models\Code;
use Illuminate\Http\Request;

/**
 * @group Category(카테고리)
 */
class CodeController extends Controller
{

    /**
     * 상품 카테고리 목록
     * @priority 1
     * @responseFile storage/responses/codes.json
     */
    public function products(Request $request, $id = 1)
    {
        $items = Code::getItems($id, false);
        return CodeResource::collection($items);
    }



}
