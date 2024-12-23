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

}
