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

    public function setDefault()
    {
        $deliveryAddresses = auth()->user()->deliveryAddresses();

        //배송지가 하나면 기본배송지로 set
        if ($deliveryAddresses->count() === 1) {
            $this->update(['is_default' => true]);
        }

        //기본배송지가 true이면 나머지 배송지는 false
        if ($this->is_default) {
            $deliveryAddresses->whereNotIn('id', [$this->id])->update(['is_default' => false]);
        }
    }

}
