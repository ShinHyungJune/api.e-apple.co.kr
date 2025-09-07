#!/usr/bin/env php
<?php

/**
 * SMS 발송 테스트 스크립트
 * 사용법: php test_sms.php
 */

// API 엔드포인트
$apiUrl = 'http://localhost:8000/api/admin/orders/test';

// 테스트 요청 데이터
$data = [
    'test_sms' => true
];

// cURL 초기화
$ch = curl_init($apiUrl);

// cURL 옵션 설정
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    // 관리자 인증 토큰이 필요한 경우 추가
    // 'Authorization: Bearer YOUR_TOKEN_HERE'
]);

// 요청 실행
echo "SMS 발송 테스트 시작...\n";
echo "대상 번호: 01030217486\n";
echo "API 호출 중...\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 응답 처리
if ($response === false) {
    echo "오류: " . curl_error($ch) . "\n";
} else {
    echo "HTTP 응답 코드: $httpCode\n";
    echo "응답: " . $response . "\n\n";
    
    if ($httpCode == 200) {
        echo "✅ SMS 발송 요청이 성공적으로 처리되었습니다.\n";
        echo "📱 01030217486 번호로 문자가 발송되었습니다.\n";
        echo "\n예상 메시지 내용:\n";
        echo "-------------------------------\n";
        echo "안녕하세요 고객님, 열매나무를 이용해주셔서 감사합니다.\n";
        echo "고객님의 상품이 아래와 같이 출고될 예정이니 참고 부탁드립니다.\n\n";
        echo "# 출고정보\n";
        echo "- 택배사 : cj\n";
        echo "- 운송장번호 : TEST123456789\n";
        echo "- 출고상품 : [상품명]\n";
        echo "-------------------------------\n";
    } else {
        echo "❌ SMS 발송 요청이 실패했습니다.\n";
    }
}

// cURL 종료
curl_close($ch);