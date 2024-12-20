<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Iamport extends Model
{
    use HasFactory;

    const PAYMENT_INTEGRATION = false;

    // 결제취소
    public static function cancel($accessToken, $imp_uid, $amount = 0, $message = "결제실패")
    {
        return Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => $accessToken
        ])->post("http://api.iamport.kr/payments/cancel", [
            "reason" => $message,
            "imp_uid" => $imp_uid,
            "amount" => $amount
        ])->json();
    }

    // 아임포트 결제요청 권한 얻기
    public static function getAccessToken()
    {
        $response = Http::withHeaders([
            "Content-Type" => "application/json"
        ])->post("http://api.iamport.kr/users/getToken", [
            "imp_key" => config("iamport.key"),
            "imp_secret" => config("iamport.secret"),
        ])->json();

        return $response["response"]["access_token"];
    }

    // 아임포트 주문조회
    public static function getOrder($accessToken, $imp_uid)
    {
        return Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => $accessToken
        ])->get("http://api.iamport.kr/payments/{$imp_uid}", [
            "imp_key" => config("iamport.key"),
            "imp_secret" => config("iamport.secret"),
        ])->json()["response"];
    }

    // 영수증 조회 (이건 현금영수증이 아니라 그냥 결제영수증인듯)
    public static function getBill($accessToken, $imp_uid)
    {
        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => $accessToken
        ])->get("http://api.iamport.kr/payments/{$imp_uid}", [
            "imp_key" => config("iamport.key"),
            "imp_secret" => config("iamport.secret"),
        ])->json()["response"];

        if($response)
            return $response['receipt_url'];

        return null;
    }
}
