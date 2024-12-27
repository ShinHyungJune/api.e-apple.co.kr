<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MainResource;

class MainController extends ApiController
{
    public function index()
    {
        /*
            BANNER
            오늘의 특가로 만나는 신선한 과일 product/sale
            오늘의 당도 체크
            열매나무 인기상품 product/popular
            이달의 추천 상품(MD 추천 선물 타입)
               => (국산, 수입, 제출, 가공품, 대용량, 소용량)
            베스트 상품 모음 product/best
            과즙이 많은 과일 모음 product/juicy
            오늘의 후기
        */

        $items = [];

        return $this->respondSuccessfully(MainResource::make($items));

    }
}
