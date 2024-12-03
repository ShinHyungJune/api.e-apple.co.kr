<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'accepted_if' => 'The :attribute must be accepted when :other is :value.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => ':attribute(은)는 :date 이후여야 합니다..',
    'after_or_equal' => ':attribute(은)는 :date보다 이후거나 같아야 합니다.',
    'alpha' => 'The :attribute must only contain letters.',
    'alpha_dash' => 'The :attribute must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute must only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => ':attribute(은)는 :date보다 이전이어야 합니다.',
    'before_or_equal' => ':attribute(은)는 :date보다 같거나 이전이어야 합니다.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => ':attribute가 일치하지 않습니다.',
    'current_password' => 'The password is incorrect.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => ':attribute(은)는 이메일 형태여야 합니다.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => ':attribute (은)는 :max를 초과할 수 없습니다.',
        'file' => ':attribute (은)는 :max kilobytes를 초과할 수 없습니다.',
        'string' => 'The :attribute must not be greater than :max characters.',
        'array' => 'The :attribute must not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => ':attribute(은)는  최소 :min 이상이어야 합니다.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => ':attribute(은)는 최소 :min 자 이상이어야 합니다.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => ':attribute(을)를 입력해주세요.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid timezone.',
    'unique' => ':attribute(이)가 이미 사용되고 있습니다.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute must be a valid URL.',
    'uuid' => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        "contact" => "휴대폰번호",
        "name" => "이름",
        "address" => "주소",
        "email" => "이메일",
        "img" => "이미지",
        "category_id" => "카테고리",
        "category" => "카테고리",
        "time" => "시간",
        'price' => "금액",
        "style" => "스타일",
        "comment" => "전달사항",
        "required_at" => "필요일자",
        "password" => "비밀번호",
        "delivery_started_at" => "첫 수령일",
        "address_zipcode" => "우편번호",
        "address_detail" => "상세주소",
        "number" => "번호",
        "delivery_title" => "배송지명",
        "delivery_name" => "받으실분",
        "delivery_contact" => "받는분 전화번호",
        "delivery_address" => "주소",
        "delivery_address_detail" => "상세주소",
        "delivery_address_zipcode" => "우편번호",
        "delivery_at" => "희망수령일",
        "point_use" => "사용적립금",
        "pay_method_id" => "결제수단",
        "reason" => "사유",
        "user_contact" => "주문자 전화번호",
        "user_name" => "주문자명",
        "title" => "제목",
        "point" => "포인트",
        "max" => "최대수",
        "finished_at" => "마감일",
        "contact_change" => "변경할 폰번호 아이디",
        "ids" => "아이디",
        "password_new" => "새 비밀번호",
        "password_new_confirmation" => "새 비밀번호",
        "distance_started_at" => "주행거리 최소",
        "distance_finished_at" => "주행거리 최대",
        "year_started_at" => "연식 최소",
        "year_finished_at" => "연식 최대",
        "price_sell_started_at" => "가격 최소",
        "price_sell_finished_at" => "가격 최대",
        "description" => "내용",
        "url" => "URL",

        "sale_id" => "매물",
        "will_purchase_at" => "구입예정일",
        "region" => "지역",
        "type_country" => "국산/수입",
        "model" => "모델",
        "fuel" => "연료",
        "color" => "색상",
        "options" => "옵션",
        "distance" => "주행거리",
        "accept_accident" => "구매가능한 사고수준",
        "budget" => "예산",
        "urls" => "URL 목록",
        "birth" => "출생년도",
        "product_id" => "상품",
        "practice_ids" => "시험",

        "job" => "직업",
        "option_ids" => "옵션",
        "option_id" => "옵션",
        "nickname" => "닉네임",
        "center_type" => "센터",

        "img_bill" => "영수증리뷰 인증샷",
        "img_store_alarm" => "알람처리 인증샷",
        "img_store_place" => "장소저장 인증샷",

        "type" => "유형",
        "premium" => "프리미엄 여부",
        "need_order" => "구매형 여부",
        "category_ids" => "카테고리",
        "url" => "URL",
        "provide" => "제공내역",
        "img" => "이미지",
        "details" => "상세이미지",
        "tags" => "태그",
        "platform" => "플랫폼",
        "application_started_at" => "신청 시작일",
        "application_finished_at" => "신청 마감일",
        "select_opened_at" => "선정 발표일",
        "review_finished_at" => "컨텐츠등록 마감일",
        "winner_at" => "결과 발표일",
        "about_policy" => "영업시간 및 방문 가능일정",
        "mission" => "캠페인 미션",
        "keyword" => "키워드",
        "link" => "링크 삽입",
        "count_letter" => "글자수",
        "video" => "비디오",
        "gender" => "성별",
        "year" => "제조연도",
        "county_id" => "위치",
        "machine_category_id" => "카테고리",
        "machine_company_id" => "제조사",
        "machine_spec_id" => "사양",
        "machine_option_id" => "옵션",
        "imgs" => "이미지",
        "state" => "상태",
        "price_sell" => "판매가",
        'business_category_id' => '업종',
        'qna_category_id' => '접수사유',
        
        "imgs_prototype" => "시안 이미지",
        "imgs_real" => "실제작품 이미지",
        "imgs_circle" => "360도 이미지",
        
        "summary" => "요약",
        "price_origin" => "상품가격",
        "price_discount" => "할인가격",
        "pop" => "인기여부",
        "special" => "특가여부",
        "recommend" => "추천여부",
        "empty" => "품절여부",
        "duration" => "제작기간",
        "texture" => "재질",
        "creator" => "제작사",
        "case" => "케이스",
        "way_to_create" => "제작방법",
        "colors" => "색상",
        "products" => "추가상품",
        "sizes" => "사이즈",
        'faq_category_id' => '카테고리',
        'started_at' => '시작일',
        'buyer_email' => '주문자 이메일',
        'buyer_address' => '주소',
        'buyer_address_detail' => '상세주소',
    ],

];
