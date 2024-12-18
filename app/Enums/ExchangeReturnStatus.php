<?php

namespace App\Enums;

enum ExchangeReturnStatus: string
{
    case RECEIVED = 'received';
    case APPROVED = 'approved';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::RECEIVED => '접수완료',
            self::APPROVED => '승인',
            self::PROCESSING => '처리중',
            self::COMPLETED => '완료됨',
            self::REJECTED => '거부됨',
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
