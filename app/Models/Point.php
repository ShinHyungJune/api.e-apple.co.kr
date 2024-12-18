<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $guarded = ['id'];

    public function model()
    {
        return $this->morphTo();
    }

    public function scopeMine(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

}
