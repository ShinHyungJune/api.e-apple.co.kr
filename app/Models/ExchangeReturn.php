<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeReturn extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const TYPE_EXCHANGE = 'exchange';

    const TYPE_RETURN = 'return';

    const TYPES = [self::TYPE_EXCHANGE, self::TYPE_RETURN];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeMine(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

}
