<?php

namespace App\Enums;

enum IamportMethod: string
{
    case CARD = 'card';

    //case VBANK = 'vbank';

    case TRANSFER = 'trans';

    public function label(): string
    {
        return match ($this) {
            self::CARD => '신용카드',
            //self::VBANK => '가상계좌',
            self::TRANSFER => '실시간 계좌이체',
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

    public function pg(): string
    {
        return match ($this) {
            self::CARD => 'html5_inicis',
            //self::VBANK => 'html5_inicis',
            self::TRANSFER => 'html5_inicis',
        };
    }

}
