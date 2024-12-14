<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    //

    public function cartProductOptions()
    {
        return $this->hasMany(CartProductOption::class, 'product_option_id');
    }
}
