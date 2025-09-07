<?php

namespace App\Enums;

enum CouponTypeMoment: string
{
    case USER_CREATE = 'USER_CREATE';
    case ORDER_CREATE_FIRST = 'ORDER_CREATE_FIRST';
    
    public function label(): string
    {
        return match ($this) {
            self::USER_CREATE => '회원가입',
            self::ORDER_CREATE_FIRST => '첫 주문',
        };
    }
    
    public static function getItems(): array
    {
        $results = [];
        foreach (self::cases() as $case) {
            $results[] = ['value' => $case->value, 'text' => $case->label()];
        }
        return $results;
    }
    
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}