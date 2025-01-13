<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductInquiry extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeMine(Builder $query)
    {
        $query->where('user_id', auth()->id());
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

        if (isset($filters['keyword'])) {
            $query->where('title', 'like', '%' . $filters['keyword'] . '%')
                ->orWhereHas('user', function ($query) use ($filters) {
                    $query->where('name', 'like', '%' . $filters['keyword'] . '%');
                });
        }
    }

}
