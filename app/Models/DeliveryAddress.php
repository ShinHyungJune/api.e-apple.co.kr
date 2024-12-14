<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeMine(Builder $query)
    {
        $query->where('user_id', auth()->id());
    }

}
