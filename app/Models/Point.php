<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Point extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const TYPE_EXPIRATION = 'expiration';
    const EXPIRATION_DAYS = 1;


    const TYPES = [self::TYPE_DEPOSIT, self::TYPE_WITHDRAWAL, self::TYPE_EXPIRATION];


    /**
     * @deprecated
     */
    public function model()
    {
        return $this->morphTo();
    }

    public function pointable()
    {
        return $this->morphTo();
    }

    public function scopeMine(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeSearch(Builder $query, $filters)
    {
        if (!empty($filters['type'])) {
            if (in_array($filters['type'], self::TYPES)) {
                if ($filters['type'] === self::TYPE_EXPIRATION) {
                    $query->whereNotNull('expired_at');
                } else {
                    $query->whereNull('expired_at');
                    if ($filters['type'] === self::TYPE_DEPOSIT) {
                        $query->where('deposit', '>', 0);
                    }
                    if ($filters['type'] === self::TYPE_WITHDRAWAL) {
                        $query->where('withdrawal', '>', 0);
                    }
                }
            }
        }
        if (isset($filters['keyword'])) {
            $query->whereHas('user', function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['keyword'] . '%');
            });
        }
    }

    public function getOrderIdAttribute()
    {
        if ($this->pointable_type === Order::class && $this->pointable) {
            return $this->pointable->merchant_uid;
        }

        if ($this->pointable_type === ProductReview::class && $this->pointable) {
            return $this->pointable?->order?->merchant_uid;
        }

        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
