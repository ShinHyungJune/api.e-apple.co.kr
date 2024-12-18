<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderProduct extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function productOption(): HasOne
    {
        return $this->hasOne(ProductOption::class, 'id', 'product_option_id');
    }

}
