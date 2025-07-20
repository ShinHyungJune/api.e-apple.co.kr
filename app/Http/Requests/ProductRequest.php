<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'], // 상품명: 필수, 문자열, 최대 255자
            'description' => ['nullable', 'string'], // 상품설명: 선택적, 문자열

            'options' => ['required', 'array', 'min:1'],
            'options.*.id' => ['nullable'],
            'options.*.name' => ['required', 'string', 'max:255'],
            'options.*.price' => ['required', 'numeric', 'min:0'],
            'options.*.original_price' => ['nullable', 'numeric', 'min:0'],
            'options.*.stock_quantity' => ['required', 'numeric', 'min:0'],

            'imgs' => ['nullable', 'array'],
            'imgs.*' => ['nullable', 'file', 'mimes:jpg,png,pdf'/*, 'max:2048'*/], // 각각의 파일에 대해 유효성 검사
            /*'product_desc_images' => ['nullable', 'array'],
            'product_desc_images.*' => ['nullable', 'file', 'mimes:jpg,png,pdf', 'max:2048'], // 각각의 파일에 대해 유효성 검사*/

            'price' => ['required', 'numeric', 'min:0'], // 가격: 필수, 숫자, 0 이상
            'original_price' => ['nullable', 'numeric', 'min:0'], // 원래가격: 선택적, 숫자, 0 이상
            'delivery_fee' => ['nullable', 'numeric', 'min:0'], // 배송비: 필수, 숫자, 0 이상
            'stock_quantity' => ['nullable', 'integer', 'min:0'], // 재고수량: 필수, 정수, 0 이상
            'categories' => ['nullable', 'array'], // 카테고리
            //'is_md_suggestion_gift' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],

            'food_type' => ['nullable', 'string', 'max:100'], // 식품의 유형: 선택적, 문자열, 최대 100자
            'fruit_size' => ['nullable', 'string', 'max:50'], // 과일 크기: 선택적, 문자열, 최대 50자
            'sugar_content' => ['nullable', 'string', 'max:50'], // 당도: 선택적, 문자열, 최대 50자

            'shipping_origin' => ['nullable', 'string', 'max:255'], // 출고지: 선택적, 문자열, 최대 255자
            'manufacturer_and_location' => ['nullable', 'string', 'max:255'], // 생산자 및 소재지: 선택적, 문자열, 최대 255자
            'importer' => ['nullable', 'string', 'max:255'], // 수입자: 선택적, 문자열, 최대 255자
            'origin' => ['nullable', 'string', 'max:255'], // 원산지: 선택적, 문자열, 최대 255자
            'ingredients_and_composition' => ['nullable', 'string'], // 원재료 및 합량: 선택적, 문자열
            'storage_and_handling' => ['nullable', 'string', 'max:255'], // 보관/취급방법: 선택적, 문자열, 최대 255자

            //'manufacture_date' => ['nullable', 'date'], // 제조연원일: 선택적, 유효한 날짜
            //'expiration_date' => ['nullable', 'date', 'after_or_equal:manufacture_date'], // 유통기한: 제조연원일 이후
            'manufacture_date' => ['nullable', 'string'], // 제조연원일
            'expiration_date' => ['nullable', 'string'], // 유통기한

            'gmo_desc' => ['nullable', 'string', 'max:255'], // 유전자변형 농산물 여부: 필수, 불리언
            'customer_service_contact' => ['nullable', 'string', 'max:255'], // 소비자상담문의: 선택적, 문자열, 최대 255자
            'is_display' => ['nullable', 'boolean'],
        ];
    }

    public function bodyParameters()
    {
        return [
            'name' => ['description' => '<span class="point">상품명</span>'],
            'description' => ['description' => '<span class="point">상품설명</span>'],
            'options' => ['description' => '<span class="point">상품 옵션</span>'],
            'product_images' => ['description' => '<span class="point">상품 이미지</span>'],
            //'product_desc_images' => ['description' => '<span class="point">상품설명 이미지</span>'],

            'price' => ['description' => '<span class="point">가격</span>'],
            'original_price' => ['description' => '<span class="point">원래가격</span>'],
            'delivery_fee' => ['description' => '<span class="point">배송비</span>'],

            'stock_quantity' => ['description' => '<span class="point">재고수량</span>'],
            'categories' => ['description' => '<span class="point">카테고리</span>'],
            //'is_md_suggestion_gift' => ['description' => '<span class="point">MD 추천 선물</span>'],
            'tags' => ['description' => '<span class="point">태그(ex: 실시간 인기, 클래식 과일, 어른을 위한 픽, 추가 증정)</span>'],


            'food_type' => ['description' => '<span class="point">식품의 유형</span>'],
            'fruit_size' => ['description' => '<span class="point">과일 크기</span>'],
            'sugar_content' => ['description' => '<span class="point">당도</span>'],

            'shipping_origin' => ['description' => '<span class="point">출고지</span>'],
            'manufacturer_and_location' => ['description' => '<span class="point">생산자 및 소재지</span>'],
            'importer' => ['description' => '<span class="point">수입자</span>'],
            'origin' => ['description' => '<span class="point">원산지</span>'],
            'ingredients_and_composition' => ['description' => '<span class="point">원재료 및 합량</span>'],
            'storage_and_handling' => ['description' => '<span class="point">보관/취급방법</span>'],
            'manufacture_date' => ['description' => '<span class="point">제조연원일</span>'],
            'expiration_date' => ['description' => '<span class="point">유통기한</span>'],
            'gmo_desc' => ['description' => '<span class="point">유전자변형 농산물여부</span>'],
            'customer_service_contact' => ['description' => '<span class="point">소비자상담문의</span>'],
        ];
    }

    public function prepareForValidation()
    {
        $inputs = $this->input();
        foreach ($inputs as $key => $input) {
            if ($input === 'null') $inputs[$key] = null;
            if ($input === 'true') $inputs[$key] = true;
            if ($input === 'false') $inputs[$key] = false;
        }
        $inputs['options'] = json_decode($inputs['options'], true);
        $this->merge($inputs);
    }

}
