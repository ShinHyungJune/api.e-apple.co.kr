<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{

    public function scopeMine(Builder $query)
    {
        $query->where('user_id', auth()->id());
    }

    public function scopeUnused(Builder $query)
    {
        $query->where('expired_at', '>=', now())->whereNull('used_at');
    }

}
