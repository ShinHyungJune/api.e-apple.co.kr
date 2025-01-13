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

    protected $appends = ['type_label'];

    const TYPE_EXCHANGE = 'exchange';

    const TYPE_RETURN = 'return';

    const TYPES = [self::TYPE_EXCHANGE, self::TYPE_RETURN];

    public function scopeSearch(Builder $query, $filters): Builder
    {
        $filters = json_decode($filters);
        /*if (!empty($filters->keyword)) {
            return $query->where('merchant_uid', 'like', '%' . $filters->keyword . '%')
                ->orWhere('buyer_name', 'like', '%' . $filters->keyword . '%');
        }*/
        return $query;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function scopeMine(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

    public function getTypeLabelAttribute(): string
    {
        if ($this->type === self::TYPE_EXCHANGE) return '교환';
        if ($this->type === self::TYPE_RETURN) return '반품';
        return '';
    }

}
