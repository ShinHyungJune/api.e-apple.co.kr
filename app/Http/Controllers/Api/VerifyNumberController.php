<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserRequest;
use App\Http\Requests\VerifyNumberRequest;
use App\Models\SMS;
use App\Models\VerifyNumber;
use Carbon\Carbon;

class VerifyNumberController extends ApiController
{
    /**
     * @group VerifyNumber(인증번호)
     * @unauthenticated
     */
    public function store(VerifyNumberRequest $request)
    {
        $request['contact'] = str_replace("-", "",$request->contact);

        $countRecentTry = VerifyNumber::where('ip', $request->ip())
            ->where('created_at', ">=", Carbon::now()->subMinute())
            ->count();

        if($countRecentTry > 10)
            return $this->respondForbidden('1분 뒤에 재시도해주세요.');

        $verifyNumber = VerifyNumber::create([
            'ids' => $request->contact,
            'number' => rand(100000,999999),
            'ip' => $request->ip(),
        ]);

        $sms = new SMS();

        $sms->send("+82".$request->contact, "[인증번호]", "인증번호가 발송되었습니다. ".$verifyNumber->number."\n\n"."-".config("app.name")."-");

        return $this->respondSuccessfully();
    }

    /**
     * @group VerifyNumber(인증번호)
     * @unauthenticated
     */
    public function update(VerifyNumberRequest $request)
    {
        $verifyNumber = VerifyNumber::where('ids', $request->contact)
            ->where('number', $request->number)
            ->first();

        if(!$verifyNumber)
            return $this->respondForbidden('유효하지 않은 인증번호입니다.');

        $verifyNumber->update(['verified' => 1]);

        return $this->respondSuccessfully();
    }
}
