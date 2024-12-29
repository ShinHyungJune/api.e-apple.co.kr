<?php

namespace App\Enums;

enum ProductCategory: string
{
    case SUGGESTION = 'suggestion';
    case BEST = 'best';
    case GIFT = 'gift';
    case SALE = 'sale';
    case POPULAR = 'popular';
    case JUICY = 'juicy';

    /*case STORY = 4;
    case EVENT = 5;*/

    public function label(): string
    {
        return match ($this) {
            self::SUGGESTION => '추천',
            self::BEST => '베스트',
            self::GIFT => '선물',
            self::SALE => '특가',
            self::POPULAR => '인기상품',
            self::JUICY => '과즙이 많은 과일',
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
