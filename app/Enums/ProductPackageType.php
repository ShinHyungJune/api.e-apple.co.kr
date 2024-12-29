<?php

namespace App\Enums;

enum ProductPackageType: string
{
    //
    case MD_SUGGESTION = 'md_suggestion';
    case MONTHLY_SUGGESTION = 'monthly_suggestion';

    public function label(): string
    {
        return match ($this) {
            self::MD_SUGGESTION => 'MD 추천',
            self::MONTHLY_SUGGESTION => '이달의 추천',
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
