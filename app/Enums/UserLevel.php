<?php

namespace App\Enums;

enum UserLevel: string
{
    case GENERAL = 'general';
    case FAMILY = 'family';
    case VIP = 'vip';

    public function label(): string
    {
        return match ($this) {
            self::GENERAL => '일반',
            self::FAMILY => 'FAMILY',
            self::VIP => 'VIP',
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

    public function purchaseRewardPointsRate(): int
    {
        return match ($this) {
            self::GENERAL => 1,
            self::FAMILY => 2,
            self::VIP => 3,
        };
    }

    public function lastMonthPurchaseAmounts(): int
    {
        return match ($this) {
            self::GENERAL => 0,
            self::FAMILY => 100000,
            self::VIP => 200000,
        };
    }
}
