<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    //

    public function cartProductOptions()
    {
        return $this->hasMany(CartProductOption::class, 'product_option_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
