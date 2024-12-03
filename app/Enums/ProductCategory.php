<?php

namespace App\Enums;

enum ProductCategory: string
{
    case SUGGESTION = 'suggestion';
    case BEST = 'best';
    case GIFT = 'gift';

    /*case STORY = 4;
    case EVENT = 5;*/

    public function label(): string
    {
        return match ($this) {
            self::SUGGESTION => '추천',
            self::BEST => '베스트',
            self::GIFT => '선물',
            /*self::STORY => '스토리',
            self::EVENT => '이벤트',*/
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
