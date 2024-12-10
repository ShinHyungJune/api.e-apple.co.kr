<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class VerifyNumber extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function check($ids)
    {
        $verifyNumber = VerifyNumber::where('ids', $ids)->where('verified', true)->first();
        if (!$verifyNumber) {
            return throw ValidationException::withMessages(["contact" => ["연락처를 인증해주세요."]]);
        }
        $verifyNumber->delete();
        return true;
    }

}
