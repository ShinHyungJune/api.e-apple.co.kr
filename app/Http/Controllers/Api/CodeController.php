<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductPackageType;
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
    public function products(Request $request, $id = Code::PRODUCT_CATEGORY_ID)
    {
        $items = Code::getItems($id, false);
        return CodeResource::collection($items);
    }


    /**
     * 이달의 추천 상품 카테고리 목록
     * @queryParam type String monthly_suggestion
     * @priority 1
     * @responseFile storage/responses/codes.json
     */
    public function packages(Request $request, $type)
    {
        if ($type === ProductPackageType::MONTHLY_SUGGESTION->value) {
            $items = Code::getItems(Code::MONTHLY_SUGGESTION_CATEGORY_ID, false);
            return CodeResource::collection($items);
        }
    }

}
