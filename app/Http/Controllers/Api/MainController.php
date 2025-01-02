<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductPackageType;
use App\Http\Resources\MainResource;
use App\Models\Banner;
use App\Models\Product;
use App\Models\ProductPackage;
use App\Models\ProductReview;
use App\Models\Sweetness;

/**
 * @group Main
 */
class MainController extends ApiController
{
    /**
     * 메인페이지
     * @priority 1
     * @responseFile storage/responses/main.json
     */
    public function index()
    {
        $today = now()->format('Y-m-d');

        $items = [];
        $items['banners'] = Banner::where('is_active', true)->get()->load('media');

        //오늘의 특가로 만나는 신선한 과일 product/sale
        $items['saleProducts'] = Product::with('reviews', 'media')->withCount(['reviews', 'inquiries'])->category('sale')->latest()->limit(4)->get();
        //열매나무 인기상품 product/popular
        $items['popularProducts'] = Product::with('reviews', 'media')->withCount(['reviews', 'inquiries'])->category('popular')->latest()->limit(4)->get();
        //베스트 상품 모음 product/best
        $items['bestProducts'] = Product::with('reviews', 'media')->withCount(['reviews', 'inquiries'])->category('best')->latest()->limit(10)->get();
        // 과즙이 많은 과일 모음 product/juicy
        $items['juicyProducts'] = Product::with('reviews', 'media')->withCount(['reviews', 'inquiries'])->category('juicy')->latest()->limit(10)->get();

        //오늘의 후기
        $items['reviews'] = ProductReview::with(['user','media'])
            //->where('created_at', 'like', $today . '%')
            ->latest()->limit(3)->get()
            ->filter(function ($review) use ($today) {
                return strpos($review->created_at, $today) === 0;
            });

        //오늘의 당도 체크
        $items['sweetnesses'] = Sweetness::with('media')
            //->where('created_at', 'like', $today . '%')
            ->where('is_display', true)
            ->latest()->limit(10)->get()
            /*->filter(function (sweetness) use ($today) {
                return strpos(sweetness->created_at, $today) === 0;
            })*/
        ;

        //이달의 추천 상품(MD 추천 선물 타입) => (국산, 수입, 제출, 가공품, 대용량, 소용량)
        $items['monthlySuggestionProducts'] = ProductPackage::with(['products.media', 'products.reviews', 'products.inquiries', 'media'])->has('products')
            ->where('type', ProductPackageType::MONTHLY_SUGGESTION)
            ->latest()->get(); //->groupBy('category_id');
        //return ProductPackageResource::collection($items['monthlySuggestionProducts']);

        return $this->respondSuccessfully(MainResource::make(collect($items)));
    }
}
