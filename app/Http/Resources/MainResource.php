<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        Carbon::setLocale('ko');

        $return = [
            //...parent::toArray($request);
            /*...$this->only([
                'banners', 'saleProducts', 'popularProducts', 'bestProducts', 'juicyProducts', 'reviews',
            ]),*/
            'banners' => !empty($this->get('banners')) ? BannerResource::collection($this->get('banners')) : null,
            'saleProducts' => !empty($this->get('saleProducts')) ? ProductResource::collection($this->get('saleProducts')) : null,
            'popularProducts' => !empty($this->get('popularProducts')) ? ProductResource::collection($this->get('popularProducts')) : null,
            'bestProducts' => !empty($this->get('bestProducts')) ? ProductResource::collection($this->get('bestProducts')) : null,
            'juicyProducts' => !empty($this->get('juicyProducts')) ? ProductResource::collection($this->get('juicyProducts')) : null,

            'sweetness' => !empty($this->get('sweetnesses')) ? [
                'standard_datetime' => Carbon::parse($this->get('sweetnesses')->max('updated_at'))->translatedFormat('d일(D) H시'),
                'items' => SweetnessResource::collection($this->get('sweetnesses'))
            ] : null,

            'reviews' => !empty($this->get('reviews')) ? ProductReviewResource::collection($this->get('reviews')) : null,
            //'monthlySuggestionProducts' => !empty($this->get('monthlySuggestionProducts')) ? ProductPackageCategoryResource::make($this->get('monthlySuggestionProducts')) : null,
            'monthlySuggestionProducts' => !empty($this->get('monthlySuggestionProducts')) ? ProductPackageResource::collection($this->get('monthlySuggestionProducts')) : null,
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [];
            //return getScribeResponseFile($return, 'products', $comments);
        }
        //*/
        return $return;
        //*/

    }
}
