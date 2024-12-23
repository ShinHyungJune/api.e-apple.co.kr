<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInquiry extends Model
{
    //
    use HasFactory;

    protected $guarded = ['id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeSearch(Builder $query, $filters)
    {
        if (isset($filters['is_answered'])) {
            if ($filters['is_answered']) {
                $query->whereNotNull('answered_at');
            } else {
                $query->whereNull('answered_at');
            }
        }
    }

}
