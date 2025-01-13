<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCoupon extends Model
{
    use HasFactory, SoftDeletes;

    public function scopeMine(Builder $query)
    {
        $query->where('user_id', auth()->id());
    }

    public function scopeUnused(Builder $query)
    {
        $query->where('expired_at', '>=', now())->whereNull('used_at');
    }

    public function scopeSearch(Builder $query, $filters)
    {
        if (isset($filters['keyword'])) {
            $query->whereHas('user', function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['keyword'] . '%');
            })->orWhereHas('coupon', function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['keyword'] . '%');
            });
        }
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
